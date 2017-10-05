<?php

declare(strict_types=1);

namespace Chubbyphp\Deserialization\Mapping;

use Chubbyphp\Deserialization\Accessor\PropertyAccessor;
use Chubbyphp\Deserialization\Denormalizer\FieldDenormalizer;
use Chubbyphp\Deserialization\Denormalizer\FieldDenormalizerInterface;

final class DenormalizingFieldMappingBuilder implements DenormalizingFieldMappingBuilderInterface
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
     * @return DenormalizingFieldMappingBuilderInterface
     */
    public static function create(string $name): DenormalizingFieldMappingBuilderInterface
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
     * @return DenormalizingFieldMappingBuilderInterface
     */
    public function setGroups(array $groups): DenormalizingFieldMappingBuilderInterface
    {
        $this->groups = $groups;

        return $this;
    }

    /**
     * @param FieldDenormalizerInterface $fieldDenormalizer
     *
     * @return DenormalizingFieldMappingBuilderInterface
     */
    public function setFieldDenormalizer(
        FieldDenormalizerInterface $fieldDenormalizer
    ): DenormalizingFieldMappingBuilderInterface {
        $this->fieldDenormalizer = $fieldDenormalizer;

        return $this;
    }

    /**
     * @return DenormalizingFieldMappingInterface
     */
    public function getMapping(): DenormalizingFieldMappingInterface
    {
        return new DenormalizingFieldMapping($this->name, $this->groups, $this->fieldDenormalizer);
    }
}
