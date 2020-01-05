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
use Chubbyphp\Deserialization\Policy\NullPolicy;
use Chubbyphp\Deserialization\Policy\PolicyInterface;

final class DenormalizationFieldMappingBuilder implements DenormalizationFieldMappingBuilderInterface
{
    /**
     * @var string
     */
    private $name;

    /**
     * @deprecated
     *
     * @var array<int, string>
     */
    private $groups = [];

    /**
     * @var FieldDenormalizerInterface
     */
    private $fieldDenormalizer;

    /**
     * @var PolicyInterface|null
     */
    private $policy;

    private function __construct(string $name)
    {
        $this->name = $name;
    }

    public static function create(
        string $name,
        bool $emptyToNull = false,
        FieldDenormalizerInterface $fieldDenormalizer = null
    ): DenormalizationFieldMappingBuilderInterface {
        if (null === $fieldDenormalizer) {
            $fieldDenormalizer = new FieldDenormalizer(new PropertyAccessor($name), $emptyToNull);
        }

        $self = new self($name);
        $self->fieldDenormalizer = $fieldDenormalizer;

        return $self;
    }

    public static function createCallback(string $name, callable $callback): DenormalizationFieldMappingBuilderInterface
    {
        $self = new self($name);
        $self->fieldDenormalizer = new CallbackFieldDenormalizer($callback);

        return $self;
    }

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
     * @param \DateTimeZone $dateTimeZone
     */
    public static function createDateTime(
        string $name,
        bool $emptyToNull = false,
        \DateTimeZone $dateTimeZone = null
    ): DenormalizationFieldMappingBuilderInterface {
        $self = new self($name);
        $self->fieldDenormalizer = new DateTimeFieldDenormalizer(new PropertyAccessor($name), $emptyToNull, $dateTimeZone);

        return $self;
    }

    public static function createEmbedMany(string $name, string $class): DenormalizationFieldMappingBuilderInterface
    {
        $self = new self($name);
        $self->fieldDenormalizer = new EmbedManyFieldDenormalizer($class, new PropertyAccessor($name));

        return $self;
    }

    public static function createEmbedOne(string $name, string $class): DenormalizationFieldMappingBuilderInterface
    {
        $self = new self($name);
        $self->fieldDenormalizer = new EmbedOneFieldDenormalizer($class, new PropertyAccessor($name));

        return $self;
    }

    public static function createReferenceMany(
        string $name,
        callable $repository
    ): DenormalizationFieldMappingBuilderInterface {
        $self = new self($name);
        $self->fieldDenormalizer = new ReferenceManyFieldDenormalizer($repository, new PropertyAccessor($name));

        return $self;
    }

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
     * @deprecated
     *
     * @param array<int, string> $groups
     */
    public function setGroups(array $groups): DenormalizationFieldMappingBuilderInterface
    {
        $this->groups = $groups;

        return $this;
    }

    public function setFieldDenormalizer(
        FieldDenormalizerInterface $fieldDenormalizer
    ): DenormalizationFieldMappingBuilderInterface {
        @trigger_error(
            'Utilize third parameter of create method instead',
            E_USER_DEPRECATED
        );

        $this->fieldDenormalizer = $fieldDenormalizer;

        return $this;
    }

    public function setPolicy(PolicyInterface $policy): DenormalizationFieldMappingBuilderInterface
    {
        $this->policy = $policy;

        return $this;
    }

    public function getMapping(): DenormalizationFieldMappingInterface
    {
        return new DenormalizationFieldMapping(
            $this->name,
            $this->groups,
            $this->fieldDenormalizer,
            $this->policy ?? new NullPolicy()
        );
    }
}
