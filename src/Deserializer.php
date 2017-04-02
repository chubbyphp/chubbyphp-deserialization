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
     * @var \ReflectionProperty[]
     */
    private $reflectionProperties;

    /**
     * @param ObjectMappingRegistryInterface $objectMappingRegistry
     */
    public function __construct(ObjectMappingRegistryInterface $objectMappingRegistry)
    {
        $this->objectMappingRegistry = $objectMappingRegistry;
    }

    /**
     * @param array  $serializedData
     * @param string $class
     * @return object
     * @throws MissingMappingException
     */
    public function deserializeByClass(array $serializedData, string $class)
    {
        return $this->deserializeByObject($serializedData, new $class());
    }

    /**
     * @param array  $serializedData
     * @param object $object
     * @return object
     * @throws NotObjectException|MissingMappingException
     */
    public function deserializeByObject(array $serializedData, $object)
    {
        if (!is_object($object)) {
            throw NotObjectException::createByType(gettype($object));
        }

        $class = get_class($object);

        $objectMapping = $this->objectMappingRegistry->getObjectMappingForClass($class);

        $propertyMappingsByName = $this->getPropertyMappingsByName($objectMapping);

        foreach ($serializedData as $property => $serializedValue) {
            if (!isset($propertyMappingsByName[$property])) {
                throw MissingMappingException::createByClassAndProperty($class, $property);
            }

            $propertyMapping = $propertyMappingsByName[$property];

            $reflectionProperty = $this->getPropertyReflection($class, $property);

            $oldValue = $reflectionProperty->getValue($object);

            if (null !== $callback = $propertyMapping->getCallback()) {
                $serializedValue = $callback($this, $serializedValue, $oldValue, $object);
            }

            $reflectionProperty->setValue($object, $serializedValue);
        }

        return $object;
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
