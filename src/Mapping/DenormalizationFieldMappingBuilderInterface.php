<?php

declare(strict_types=1);

namespace Chubbyphp\Deserialization\Mapping;

use Chubbyphp\Deserialization\Denormalizer\FieldDenormalizerInterface;
use Chubbyphp\Deserialization\Policy\PolicyInterface;

/**
 * @method DenormalizationFieldMappingBuilderInterface setPolicy(PolicyInterface $policy)
 */
interface DenormalizationFieldMappingBuilderInterface
{
    public static function create(string $name): self;

    // /**
    //  * @param string                          $name
    //  * @param FieldDenormalizerInterface|null $fieldNormalizer
    //  *
    //  * @return DenormalizationFieldMappingBuilderInterface
    //  */
    // public static function create(string $name, FieldNormalizerInterface $fieldNormalizer = null): self;

    /**
     * @deprecated
     *
     * @param array<int, string> $groups
     */
    public function setGroups(array $groups): self;

    public function setFieldDenormalizer(FieldDenormalizerInterface $fieldDenormalizer): self;

    // /**
    //  * @param PolicyInterface $policy
    //  *
    //  * @return self
    //  */
    // public function setPolicy(PolicyInterface $policy): self;

    public function getMapping(): DenormalizationFieldMappingInterface;
}
