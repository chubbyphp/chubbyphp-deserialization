<?php

declare(strict_types=1);

namespace Chubbyphp\Deserialize;

use Chubbyphp\Deserialize\Mapping\ObjectMappingInterface;
use Chubbyphp\Deserialize\Mapping\PropertyMappingInterface;
use Chubbyphp\Deserialize\Registry\ObjectMappingRegistryInterface;

final class Deserializer implements DeserializerInterface
{

    /**
     * @var ObjectMappingRegistryInterface
     */
    private $objectMappingRegistry;

    /**
     * @var bool
     */
    private $emptyStringToNull;

    /**
     * @var \ReflectionProperty[]
     */
    private $reflectionProperties;

    /**
     * @param ObjectMappingRegistryInterface $objectMappingRegistry
     * @param bool $emptyStringToNull
     */
    public function __construct(ObjectMappingRegistryInterface $objectMappingRegistry, bool $emptyStringToNull = true)
    {
        $this->objectMappingRegistry = $objectMappingRegistry;
        $this->emptyStringToNull = $emptyStringToNull;
    }

    /**
     * @param array  $serializedData
     * @param string $class
     * @return object
     */
    public function deserializeByClass(array $serializedData, string $class)
    {
        $objectMapping = $this->objectMappingRegistry->getObjectMappingForClass($class);

        $method = $objectMapping->getConstructMethod();

        $object = $class::$method();

        $this->updateProperties($objectMapping, $object, $class, $serializedData);

        return $object;
    }

    /**
     * @param array  $serializedData
     * @param object $object
     * @return object
     * @throws NotObjectException
     */
    public function deserializeByObject(array $serializedData, $object)
    {
        if (!is_object($object)) {
            throw NotObjectException::createByType(gettype($object));
        }

        $class = get_class($object);

        $objectMapping = $this->objectMappingRegistry->getObjectMappingForClass($class);

        $this->updateProperties($objectMapping, $object, $class, $serializedData);

        return $object;
    }

    /**
     * @param ObjectMappingInterface $objectMapping
     * @param $object
     * @param string $class
     * @param array $serializedData
     */
    private function updateProperties(ObjectMappingInterface $objectMapping, $object, string $class, array $serializedData)
    {
        $propertyMappingsByName = $this->getPropertyMappingsByName($objectMapping);

        foreach ($serializedData as $property => $serializedValue) {
            if (!isset($propertyMappingsByName[$property])) {
                continue;
            }

            $propertyMapping = $propertyMappingsByName[$property];

            $reflectionProperty = $this->getPropertyReflection($class, $property);

            $newValue = $propertyMapping->getPropertyDeserializer()->deserializeProperty(
                $this,
                $serializedValue,
                $reflectionProperty->getValue($object),
                $object
            );

            if ($this->emptyStringToNull && '' === $newValue) {
                $newValue = null;
            }

            $reflectionProperty->setValue($object, $newValue);
        }
    }

    /**
     * @param ObjectMappingInterface $objectMapping
     * @return PropertyMappingInterface[]
     */
    private function getPropertyMappingsByName(ObjectMappingInterface $objectMapping): array
    {
        $propertyMappings = [];
        foreach ($objectMapping->getPropertyMappings() as $propertyMapping) {
            $propertyMappings[$propertyMapping->getName()] = $propertyMapping;
        }

        return $propertyMappings;
    }

    /**
     * @param string $class
     * @param string $property
     * @return \ReflectionProperty
     */
    private function getPropertyReflection(string $class, string $property): \ReflectionProperty
    {
        $reflectionPropertyKey = $class . '::' . $property;

        if (!isset($this->reflectionProperties[$reflectionPropertyKey])) {
            $reflectionProperty = new \ReflectionProperty($class, $property);
            $reflectionProperty->setAccessible(true);

            $this->reflectionProperties[$reflectionPropertyKey] = $reflectionProperty;
        }

        return $this->reflectionProperties[$reflectionPropertyKey];
    }
}
