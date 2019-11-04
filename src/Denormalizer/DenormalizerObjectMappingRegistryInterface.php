<?php

declare(strict_types=1);

namespace Chubbyphp\Deserialization\Denormalizer;

use Chubbyphp\Deserialization\DeserializerLogicException;
use Chubbyphp\Deserialization\Mapping\DenormalizationObjectMappingInterface;

interface DenormalizerObjectMappingRegistryInterface
{
    /**
     * @throws DeserializerLogicException
     */
    public function getObjectMapping(string $class): DenormalizationObjectMappingInterface;
}
