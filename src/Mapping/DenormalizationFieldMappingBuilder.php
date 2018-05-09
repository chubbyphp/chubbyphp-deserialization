<?php

declare(strict_types=1);

namespace Chubbyphp\Deserialization\Mapping;

use Chubbyphp\Deserialization\Accessor\PropertyAccessor;
use Chubbyphp\Deserialization\Denormalizer\FieldDenormalizer;
use Chubbyphp\Deserialization\Denormalizer\FieldDenormalizerInterface;

final class DenormalizationFieldMappingBuilder implements DenormalizationFieldMappingBuilderInterface
{
    /**
     * @var string
     */
    private $name;

    /**
     * @var array
     */
    private $groups = [];

    /**
     * @var FieldDenormalizerInterface
     */
    private $fieldDenormalizer;

    /**
     * @var string|null
     */
    private $forceType;

    private function __construct()
    {
    }

    /**
     * @param string $name
     *
     * @return DenormalizationFieldMappingBuilderInterface
     */
    public static function create(string $name): DenormalizationFieldMappingBuilderInterface
    {
        $self = new self();
        $self->name = $name;
        $self->fieldDenormalizer = new FieldDenormalizer(new PropertyAccessor($name));

        return $self;
    }

    /**
     * @param array $groups
     *
     * @return DenormalizationFieldMappingBuilderInterface
     */
    public function setGroups(array $groups): DenormalizationFieldMappingBuilderInterface
    {
        $this->groups = $groups;

        return $this;
    }

    /**
     * @param FieldDenormalizerInterface $fieldDenormalizer
     *
     * @return DenormalizationFieldMappingBuilderInterface
     */
    public function setFieldDenormalizer(
        FieldDenormalizerInterface $fieldDenormalizer
    ): DenormalizationFieldMappingBuilderInterface {
        $this->fieldDenormalizer = $fieldDenormalizer;

        return $this;
    }

    /**
     * @param string|null $forceType
     *
     * @return DenormalizationFieldMappingBuilderInterface
     */
    public function setForceType(string $forceType = null): DenormalizationFieldMappingBuilderInterface
    {
        $this->forceType = $forceType;

        return $this;
    }

    /**
     * @return DenormalizationFieldMappingInterface
     */
    public function getMapping(): DenormalizationFieldMappingInterface
    {
        return new DenormalizationFieldMapping($this->name, $this->groups, $this->fieldDenormalizer, $this->forceType);
    }
}
