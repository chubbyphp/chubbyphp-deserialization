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
     * @param array $data
     * @param string $class
     * @return object
     */
    public function deserializeFromArray(array $data, string $class)
    {
        $objectMapping = $this->objectMappingRegistry->getObjectMappingForClass($class);

        $name = $objectMapping->getClass();

        $object = new $name;

        $propertyMappingsByName = $this->getPropertyMappingsByName($objectMapping);

        foreach ($data as $property => $value) {
            if (!isset($propertyMappingsByName[$property])) {
                throw new \RuntimeException(sprintf('Missing property mapping %s on class %s', $property, $name));
            }

            $propertyMapping = $propertyMappingsByName[$property];

            if (null !== $callback = $propertyMapping->getCallback()) {
                $value = $callback($value, $this);
            }

            $reflectionProperty = new \ReflectionProperty($name, $property);
            $reflectionProperty->setAccessible(true);
            $reflectionProperty->setValue($object, $value);
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
