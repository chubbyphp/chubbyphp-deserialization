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

/**
 * @deprecated Use {@link DenormalizationFieldMappingFactory} instead
 */
final class DenormalizationFieldMappingBuilder
{
    private string $name;

    private FieldDenormalizerInterface $fieldDenormalizer;

    private ?PolicyInterface $policy;

    private function __construct(string $name)
    {
        $this->name = $name;
    }

    public static function create(
        string $name,
        bool $emptyToNull = false,
        ?FieldDenormalizerInterface $fieldDenormalizer = null
    ): self {
        @trigger_error(
            sprintf('%s:create use %s:create', self::class, DenormalizationFieldMappingFactory::class),
            E_USER_DEPRECATED
        );

        if (null === $fieldDenormalizer) {
            $fieldDenormalizer = new FieldDenormalizer(new PropertyAccessor($name), $emptyToNull);
        }

        $self = new self($name);
        $self->fieldDenormalizer = $fieldDenormalizer;

        return $self;
    }

    public static function createCallback(string $name, callable $callback): self
    {
        @trigger_error(
            sprintf('%s:createCallback use %s:createCallback', self::class, DenormalizationFieldMappingFactory::class),
            E_USER_DEPRECATED
        );

        $self = new self($name);
        $self->fieldDenormalizer = new CallbackFieldDenormalizer($callback);

        return $self;
    }

    public static function createConvertType(
        string $name,
        string $type,
        bool $emptyToNull = false
    ): self {
        @trigger_error(
            sprintf(
                '%s:createConvertType use %s:createConvertType',
                self::class,
                DenormalizationFieldMappingFactory::class
            ),
            E_USER_DEPRECATED
        );

        $self = new self($name);
        $self->fieldDenormalizer = new ConvertTypeFieldDenormalizer(new PropertyAccessor($name), $type, $emptyToNull);

        return $self;
    }

    public static function createDateTime(
        string $name,
        bool $emptyToNull = false,
        ?\DateTimeZone $dateTimeZone = null
    ): self {
        @trigger_error(
            sprintf(
                '%s:createDateTime use %s:createDateTime',
                self::class,
                DenormalizationFieldMappingFactory::class
            ),
            E_USER_DEPRECATED
        );

        $self = new self($name);
        $self->fieldDenormalizer = new DateTimeFieldDenormalizer(
            new PropertyAccessor($name),
            $emptyToNull,
            $dateTimeZone
        );

        return $self;
    }

    public static function createEmbedMany(string $name, string $class): self
    {
        @trigger_error(
            sprintf(
                '%s:createEmbedMany use %s:createEmbedMany',
                self::class,
                DenormalizationFieldMappingFactory::class
            ),
            E_USER_DEPRECATED
        );

        $self = new self($name);
        $self->fieldDenormalizer = new EmbedManyFieldDenormalizer($class, new PropertyAccessor($name));

        return $self;
    }

    public static function createEmbedOne(string $name, string $class): self
    {
        @trigger_error(
            sprintf(
                '%s:createEmbedOne use %s:createEmbedOne',
                self::class,
                DenormalizationFieldMappingFactory::class
            ),
            E_USER_DEPRECATED
        );

        $self = new self($name);
        $self->fieldDenormalizer = new EmbedOneFieldDenormalizer($class, new PropertyAccessor($name));

        return $self;
    }

    public static function createReferenceMany(
        string $name,
        callable $repository
    ): self {
        @trigger_error(
            sprintf(
                '%s:createReferenceMany use %s:createReferenceMany',
                self::class,
                DenormalizationFieldMappingFactory::class
            ),
            E_USER_DEPRECATED
        );

        $self = new self($name);
        $self->fieldDenormalizer = new ReferenceManyFieldDenormalizer($repository, new PropertyAccessor($name));

        return $self;
    }

    public static function createReferenceOne(
        string $name,
        callable $repository,
        bool $emptyToNull = false
    ): self {
        @trigger_error(
            sprintf(
                '%s:createReferenceOne use %s:createReferenceOne',
                self::class,
                DenormalizationFieldMappingFactory::class
            ),
            E_USER_DEPRECATED
        );

        $self = new self($name);
        $self->fieldDenormalizer = new ReferenceOneFieldDenormalizer(
            $repository,
            new PropertyAccessor($name),
            $emptyToNull
        );

        return $self;
    }

    public function setPolicy(PolicyInterface $policy): self
    {
        $this->policy = $policy;

        return $this;
    }

    public function getMapping(): DenormalizationFieldMappingInterface
    {
        return new DenormalizationFieldMapping(
            $this->name,
            $this->fieldDenormalizer,
            $this->policy ?? new NullPolicy()
        );
    }
}
