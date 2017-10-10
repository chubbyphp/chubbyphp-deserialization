<?php

declare(strict_types=1);

namespace Chubbyphp\Deserialization\Mapping;

interface DenormalizationClassToTypeMappingInterface
{
    /**
     * @return string
     */
    public function getClass(): string;

    /**
     * @return string[]
     */
    public function getTypes(): array;
}
