<?php

declare(strict_types=1);

namespace Chubbyphp\Deserialization\Mapping;

use Chubbyphp\Deserialization\Accessor\PropertyAccessor;
use Chubbyphp\Deserialization\Denormalizer\CallbackFieldDenormalizer;
use Chubbyphp\Deserialization\Denormalizer\ConvertTypeFieldDenormalizer;
use Chubbyphp\Deserialization\Denormalizer\DateTimeFieldDenormalizer;
use Chubbyphp\Deserialization\Denormalizer\FieldDenormalizer;
use Chubbyphp\Deserialization\Denormalizer\FieldDenormalizerInterface;
use Chubbyphp\Deserialization\Denormalizer\Relation\EmbedManyFieldDenormalizer;
use Chubbyphp\Deserialization\Denormalizer\Relation\EmbedOneFieldDenormalizer;
use Chubbyphp\Deserialization\Denormalizer\Relation\ReferenceManyFieldDenormalizer;
use Chubbyphp\Deserialization\Denormalizer\Relation\ReferenceOneFieldDenormalizer;

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

    private function __construct(string $name)
    {
        $this->name = $name;
    }

    /**
     * @param string $name
     *
     * @return DenormalizationFieldMappingBuilderInterface
     */
    public static function create(string $name, bool $emptyToNull = false): DenormalizationFieldMappingBuilderInterface
    {
        $self = new self($name);
        $self->fieldDenormalizer = new FieldDenormalizer(new PropertyAccessor($name), $emptyToNull);

        return $self;
    }

    /**
     * @param string   $name
     * @param callable $callback
     *
     * @return DenormalizationFieldMappingBuilderInterface
     */
    public static function createCallback(string $name, callable $callback): DenormalizationFieldMappingBuilderInterface
    {
        $self = new self($name);
        $self->fieldDenormalizer = new CallbackFieldDenormalizer($callback);

        return $self;
    }

    /**
     * @param string $name
     * @param string $type
     * @param bool   $emptyToNull
     *
     * @return DenormalizationFieldMappingBuilderInterface
     */
    public static function createConvertType(
        string $name,
        string $type,
        bool $emptyToNull = false
    ): DenormalizationFieldMappingBuilderInterface {
        $self = new self($name);
        $self->fieldDenormalizer = new ConvertTypeFieldDenormalizer(new PropertyAccessor($name), $type, $emptyToNull);

        return $self;
    }

    /**
     * @param string $name
     *
     * @return DenormalizationFieldMappingBuilderInterface
     */
    public static function createDateTime(
        string $name,
        bool $emptyToNull = false
    ): DenormalizationFieldMappingBuilderInterface {
        $self = new self($name);
        $self->fieldDenormalizer = new DateTimeFieldDenormalizer(new PropertyAccessor($name), $emptyToNull);

        return $self;
    }

    /**
     * @param string $name
     * @param string $class
     *
     * @return DenormalizationFieldMappingBuilderInterface
     */
    public static function createEmbedMany(string $name, string $class): DenormalizationFieldMappingBuilderInterface
    {
        $self = new self($name);
        $self->fieldDenormalizer = new EmbedManyFieldDenormalizer($class, new PropertyAccessor($name));

        return $self;
    }

    /**
     * @param string $name
     * @param string $class
     *
     * @return DenormalizationFieldMappingBuilderInterface
     */
    public static function createEmbedOne(string $name, string $class): DenormalizationFieldMappingBuilderInterface
    {
        $self = new self($name);
        $self->fieldDenormalizer = new EmbedOneFieldDenormalizer($class, new PropertyAccessor($name));

        return $self;
    }

    /**
     * @param string   $name
     * @param callable $repository
     *
     * @return DenormalizationFieldMappingBuilderInterface
     */
    public static function createReferenceMany(
        string $name,
        callable $repository
    ): DenormalizationFieldMappingBuilderInterface {
        $self = new self($name);
        $self->fieldDenormalizer = new ReferenceManyFieldDenormalizer($repository, new PropertyAccessor($name));

        return $self;
    }

    /**
     * @param string   $name
     * @param callable $repository
     * @param bool     $emptyToNull
     *
     * @return DenormalizationFieldMappingBuilderInterface
     */
    public static function createReferenceOne(
        string $name,
        callable $repository,
        bool $emptyToNull = false
    ): DenormalizationFieldMappingBuilderInterface {
        $self = new self($name);
        $self->fieldDenormalizer = new ReferenceOneFieldDenormalizer(
            $repository,
            new PropertyAccessor($name),
            $emptyToNull
        );

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
        return new DenormalizationFieldMapping(
            $this->name,
            $this->groups,
            $this->fieldDenormalizer
        );
    }
}
