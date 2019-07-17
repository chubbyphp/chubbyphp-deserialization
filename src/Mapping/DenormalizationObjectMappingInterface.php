<?php

declare(strict_types=1);

namespace Chubbyphp\Deserialization\Mapping;

use Chubbyphp\Deserialization\DeserializerRuntimeException;

interface DenormalizationObjectMappingInterface
{
    /**
     * @return string
     */
    public function getClass(): string;

    /**
     * @param string      $path
     * @param string|null $type
     *
     * @throws DeserializerRuntimeException
     *
     * @return callable
     */
    public function getDenormalizationFactory(string $path, string $type = null): callable;

    /**
     * @param string      $path
     * @param string|null $type
     *
     * @throws DeserializerRuntimeException
     *
     * @return DenormalizationFieldMappingInterface[]
     */
    public function getDenormalizationFieldMappings(string $path, string $type = null): array;
}
