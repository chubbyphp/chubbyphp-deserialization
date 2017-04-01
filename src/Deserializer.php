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
     * @param ObjectMappingRegistryInterface $objectMappingRegistry
     */
    public function __construct(ObjectMappingRegistryInterface $objectMappingRegistry)
    {
        $this->objectMappingRegistry = $objectMappingRegistry;
    }

    /**
     * @param array         $serializedData
     * @param object|string $object
     * @return object
     */
    public function deserializeFromArray(array $serializedData, $object)
    {
        $object = is_object($object) ? $object : new $object();

        $class = get_class($object);

        $objectMapping = $this->objectMappingRegistry->getObjectMappingForClass($class);

        $propertyMappingsByName = $this->getPropertyMappingsByName($objectMapping);

        foreach ($serializedData as $property => $serializedValue) {
            if (!isset($propertyMappingsByName[$property])) {
                throw new \RuntimeException(sprintf('Missing property mapping %s on class %s', $property, $class));
            }

            $propertyMapping = $propertyMappingsByName[$property];

            $reflectionProperty = new \ReflectionProperty($class, $property);
            $reflectionProperty->setAccessible(true);

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
}
