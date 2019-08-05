<?php

declare(strict_types=1);

namespace Chubbyphp\Deserialization\Mapping;

use Chubbyphp\Deserialization\Denormalizer\FieldDenormalizerInterface;
use Chubbyphp\Deserialization\Policy\PolicyInterface;

/**
 * @method setPolicy(PolicyInterface $policy): self
 */
interface DenormalizationFieldMappingBuilderInterface
{
    /**
     * @param string $name
     *
     * @return self
     */
    public static function create(string $name): self;

    /**
     * @param string                        $name
     * @param FieldNormalizerInterface|null $fieldNormalizer
     *
     * @return NormalizationFieldMappingBuilderInterface
     */
    //public static function create(string $name, FieldNormalizerInterface $fieldNormalizer = null): self;

    /**
     * @deprecated
     *
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

    /**
     * @param PolicyInterface $policy
     *
     * @return self
     */
    //public function setPolicy(PolicyInterface $policy): self;

    /**
     * @return DenormalizationFieldMappingInterface
     */
    public function getMapping(): DenormalizationFieldMappingInterface;
}
