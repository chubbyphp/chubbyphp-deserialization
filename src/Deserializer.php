<?php

declare(strict_types=1);

namespace Chubbyphp\Deserialization;

use Chubbyphp\Deserialization\Mapping\ObjectMappingInterface;
use Chubbyphp\Deserialization\Mapping\PropertyMappingInterface;
use Chubbyphp\Deserialization\Registry\ObjectMappingRegistryInterface;
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
     * @param string $path
     * @return object
     */
    public function deserializeByClass(array $serializedData, string $class, string $path = '')
    {
        $objectMapping = $this->objectMappingRegistry->getObjectMappingForClass($class);

        $factory = $objectMapping->getFactory();

        $object = $factory();

        $this->updateProperties($objectMapping, $object, $class, $serializedData, $path);

        return $object;
    }

    /**
     * @param array  $serializedData
     * @param object $object
     * @param string $path
     * @return object
     * @throws NotObjectException
     */
    public function deserializeByObject(array $serializedData, $object, string $path = '')
    {
        if (!is_object($object)) {
            $this->logger->error('deserialize: object without an object given {type}', ['type' => gettype($object)]);

            throw NotObjectException::createByType(gettype($object));
        }

        $class = get_class($object);

        $objectMapping = $this->objectMappingRegistry->getObjectMappingForClass($class);

        $this->updateProperties($objectMapping, $object, $class, $serializedData, $path);

        return $object;
    }

    /**
     * @param ObjectMappingInterface $objectMapping
     * @param $object
     * @param string $class
     * @param array $serializedData
     * @param string $path
     */
    private function updateProperties(ObjectMappingInterface $objectMapping, $object, string $class, array $serializedData, $path)
    {
        $propertyMappingsByName = $this->getPropertyMappingsByName($objectMapping);

        foreach ($serializedData as $property => $serializedValue) {
            $subPath = $path !== '' ? $path . '.' . $property : $property;

            if (!isset($propertyMappingsByName[$property])) {
                $this->logger->notice('deserialize: no mapping for path {path}', ['path' => $subPath]);

                continue;
            }

            $this->logger->info('deserialize: path {path}', ['path' => $subPath]);

            $propertyMapping = $propertyMappingsByName[$property];

            $reflectionProperty = $this->getPropertyReflection($class, $property);

            $newValue = $propertyMapping->getPropertyDeserializer()->deserializeProperty(
                $subPath,
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
