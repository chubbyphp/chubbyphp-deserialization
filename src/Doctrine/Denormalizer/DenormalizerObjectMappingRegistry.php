<?php

declare(strict_types=1);

namespace Chubbyphp\Deserialization\Doctrine\Denormalizer;

use Chubbyphp\Deserialization\Denormalizer\DenormalizerObjectMappingRegistryInterface;
use Chubbyphp\Deserialization\DeserializerLogicException;
use Chubbyphp\Deserialization\Mapping\DenormalizationObjectMappingInterface;
use Doctrine\Common\Persistence\Proxy;

final class DenormalizerObjectMappingRegistry implements DenormalizerObjectMappingRegistryInterface
{
    /**
     * @var DenormalizationObjectMappingInterface[]
     */
    private $objectMappings;

    /**
     * @param array $objectMappings
     */
    public function __construct(array $objectMappings)
    {
        $this->objectMappings = [];
        foreach ($objectMappings as $objectMapping) {
            $this->addObjectMapping($objectMapping);
        }
    }

    /**
     * @param DenormalizationObjectMappingInterface $objectMapping
     */
    private function addObjectMapping(DenormalizationObjectMappingInterface $objectMapping)
    {
        $this->objectMappings[$objectMapping->getClass()] = $objectMapping;
    }

    /**
     * @param string $class
     *
     * @return DenormalizationObjectMappingInterface
     *
     * @throws DeserializerLogicException
     */
    public function getObjectMapping(string $class): DenormalizationObjectMappingInterface
    {
        $reflectionClass = new \ReflectionClass($class);

        if (in_array(Proxy::class, $reflectionClass->getInterfaceNames(), true)) {
            $class = ($reflectionClass->getParentClass())->name;
        }

        if (isset($this->objectMappings[$class])) {
            return $this->objectMappings[$class];
        }

        throw DeserializerLogicException::createMissingMapping($class);
    }
}
