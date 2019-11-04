<?php

declare(strict_types=1);

namespace Chubbyphp\Deserialization\Mapping;

use Chubbyphp\Deserialization\DeserializerRuntimeException;

interface DenormalizationObjectMappingInterface
{
    public function getClass(): string;

    /**
     * @throws DeserializerRuntimeException
     */
    public function getDenormalizationFactory(string $path, string $type = null): callable;

    /**
     * @throws DeserializerRuntimeException
     *
     * @return DenormalizationFieldMappingInterface[]
     */
    public function getDenormalizationFieldMappings(string $path, string $type = null): array;
}
