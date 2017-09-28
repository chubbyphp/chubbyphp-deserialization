<?php

declare(strict_types=1);

namespace Chubbyphp\Deserialization\Mapping;

interface DenormalizingObjectMappingInterface
{
    /**
     * @return string
     */
    public function getClass(): string;

    /**
     * @param string|null $type
     *
     * @return callable
     */
    public function getFactory(string $type = null): callable;

    /**
     * @return DenormalizingFieldMappingInterface[]
     */
    public function getDenormalizingFieldMappings(): array;
}
