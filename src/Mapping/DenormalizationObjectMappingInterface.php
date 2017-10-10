<?php

declare(strict_types=1);

namespace Chubbyphp\Deserialization\Mapping;

interface DenormalizationObjectMappingInterface
{
    /**
     * @return DenormalizationClassToTypeMappingInterface[]
     */
    public function getDenormalizationClassToTypeMappings(): array;

    /**
     * @param string $type
     *
     * @return callable
     */
    public function getDenormalizationFactory(string $type): callable;

    /**
     * @param string $type
     *
     * @return DenormalizationFieldMappingInterface[]
     */
    public function getDenormalizationFieldMappings(string $type): array;
}
