<?php

declare(strict_types=1);

namespace Chubbyphp\Deserialization\Doctrine\Mapping;

use Chubbyphp\Deserialization\Denormalizer\FieldDenormalizer;
use Chubbyphp\Deserialization\Denormalizer\FieldDenormalizerInterface;
use Chubbyphp\Deserialization\Doctrine\Accessor\PropertyAccessor;
use Chubbyphp\Deserialization\Mapping\DenormalizationFieldMapping;
use Chubbyphp\Deserialization\Mapping\DenormalizationFieldMappingBuilderInterface;
use Chubbyphp\Deserialization\Mapping\DenormalizationFieldMappingInterface;

final class DenormalizationFieldMappingBuilder implements DenormalizationFieldMappingBuilderInterface
{
    /**
     * @var string
     */
    private $name;

    /**
     * @var array
     */
    private $groups;

    /**
     * @var FieldDenormalizerInterface
     */
    private $fieldDenormalizer;

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
        $self->groups = [];
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
     * @return DenormalizationFieldMappingInterface
     */
    public function getMapping(): DenormalizationFieldMappingInterface
    {
        return new DenormalizationFieldMapping($this->name, $this->groups, $this->fieldDenormalizer);
    }
}
