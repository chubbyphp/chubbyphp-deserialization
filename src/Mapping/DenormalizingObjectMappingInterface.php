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
     * @return callable
     */
    public function getFactory(): callable;

    /**
     * @return DenormalizingFieldMappingInterface[]
     */
    public function getDenormalizingFieldMappings(): array;
}
