<?php

declare(strict_types=1);

namespace Chubbyphp\Deserialize;

use Chubbyphp\Deserialize\Mapping\ObjectMappingInterface;
use Chubbyphp\Deserialize\Mapping\PropertyMappingInterface;
use Chubbyphp\Deserialize\Registry\ObjectMappingRegistryInterface;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;

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
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var \ReflectionProperty[]
     */
    private $reflectionProperties;

    /**
     * @param ObjectMappingRegistryInterface $objectMappingRegistry
     * @param bool $emptyStringToNull
     * @param LoggerInterface|null $logger
     */
    public function __construct(
        ObjectMappingRegistryInterface $objectMappingRegistry,
        bool $emptyStringToNull = true,
        LoggerInterface $logger = null
    ) {
        $this->objectMappingRegistry = $objectMappingRegistry;
        $this->emptyStringToNull = $emptyStringToNull;
        $this->logger = $logger ?? new NullLogger();
    }

    /**
     * @param array  $serializedData
     * @param string $class
     * @return object
     */
    public function deserializeByClass(array $serializedData, string $class)
    {
        $this->logger->info('deserialize: class {class}', ['class' => $class]);

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
            $this->logger->error('deserialize: object without an object given {type}', ['type' => gettype($object)]);

            throw NotObjectException::createByType(gettype($object));
        }

        $class = get_class($object);

        $this->logger->info('deserialize: object {class}', ['class' => $class]);

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
                $this->logger->notice('deserialize: no mapping for property {property}', ['property' => $property]);
                continue;
            }

            $this->logger->info('deserialize: property {property}', ['property' => $property]);

            $propertyMapping = $propertyMappingsByName[$property];

            $reflectionProperty = $this->getPropertyReflection($class, $property);

            $newValue = $propertyMapping->getPropertyDeserializer()->deserializeProperty(
                $serializedValue,
                $reflectionProperty->getValue($object),
                $object,
                $this
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
