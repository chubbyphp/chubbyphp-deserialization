<?php

declare(strict_types=1);

namespace Chubbyphp\Deserialization\Mapping;

use Chubbyphp\Deserialization\Denormalizer\FieldDenormalizerInterface;

interface DenormalizationFieldMappingBuilderInterface
{
    /**
     * @param string $name
     *
     * @return self
     */
    public static function create(string $name): self;

    /**
     * @param array $groups
     *
     * @return self
     */
    public function setGroups(array $groups): self;

    /**
     * @param FieldDenormalizerInterface $fieldDenormalizer
     *
     * @return self
     */
    public function setFieldDenormalizer(FieldDenormalizerInterface $fieldDenormalizer): self;

//    /**
//     * @param string|null $forceType
//     * @return self
//     */
//    public function setForceType(string $forceType = null): self;

    /**
     * @return DenormalizationFieldMappingInterface
     */
    public function getMapping(): DenormalizationFieldMappingInterface;
}
