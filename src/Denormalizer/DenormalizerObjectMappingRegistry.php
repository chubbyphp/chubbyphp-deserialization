<?php

declare(strict_types=1);

namespace Chubbyphp\Deserialization\Denormalizer;

use Chubbyphp\Deserialization\DeserializerLogicException;
use Chubbyphp\Deserialization\Mapping\DenormalizationObjectMappingInterface;

final class DenormalizerObjectMappingRegistry implements DenormalizerObjectMappingRegistryInterface
{
    /**
     * @var array<string, DenormalizationObjectMappingInterface>
     */
    private array $objectMappings = [];

    /**
     * @param array<int, DenormalizationObjectMappingInterface> $objectMappings
     */
    public function __construct(array $objectMappings)
    {
        foreach ($objectMappings as $objectMapping) {
            $this->addObjectMapping($objectMapping);
        }
    }

    /**
     * @param class-string $class
     *
     * @throws DeserializerLogicException
     */
    public function getObjectMapping(string $class): DenormalizationObjectMappingInterface
    {
        $reflectionClass = new \ReflectionClass($class);

        if (\in_array('Doctrine\Persistence\Proxy', $reflectionClass->getInterfaceNames(), true)) {
            $reflectionParentClass = (new \ReflectionClass($class))->getParentClass();
            if ($reflectionParentClass instanceof \ReflectionClass) {
                $class = $reflectionParentClass->getName();
            }
        }

        if (isset($this->objectMappings[$class])) {
            return $this->objectMappings[$class];
        }

        throw DeserializerLogicException::createMissingMapping($class);
    }

    private function addObjectMapping(DenormalizationObjectMappingInterface $objectMapping): void
    {
        $this->objectMappings[$objectMapping->getClass()] = $objectMapping;
    }
}
