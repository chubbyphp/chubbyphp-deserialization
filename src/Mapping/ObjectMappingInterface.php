<?php

declare(strict_types=1);

namespace Chubbyphp\Deserialize\Mapping;

interface ObjectMappingInterface
{
    /**
     * @return string
     */
    public function getClass(): string;

    /**
     * @return string
     */
    public function getConstructMethod(): string;

    /**
     * @return PropertyMappingInterface[]
     */
    public function getPropertyMappings(): array;
}
