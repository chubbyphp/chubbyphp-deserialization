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

final class DenormalizationFieldMappingFactory implements DenormalizationFieldMappingFactoryInterface
{
    public function create(
        string $name,
        bool $emptyToNull = false,
        ?FieldDenormalizerInterface $fieldDenormalizer = null,
        ?PolicyInterface $policy = null
    ): DenormalizationFieldMappingInterface {
        if (null === $fieldDenormalizer) {
            $fieldDenormalizer = new FieldDenormalizer(new PropertyAccessor($name), $emptyToNull);
        }

        return $this->getMapping($fieldDenormalizer, $name, $policy);
    }

    public function createCallback(
        string $name,
        callable $callback,
        ?PolicyInterface $policy = null
    ): DenormalizationFieldMappingInterface {
        $fieldDenormalizer = new CallbackFieldDenormalizer($callback);

        return $this->getMapping($fieldDenormalizer, $name, $policy);
    }

    public function createConvertType(
        string $name,
        string $type,
        bool $emptyToNull = false,
        ?PolicyInterface $policy = null
    ): DenormalizationFieldMappingInterface {
        $fieldDenormalizer = new ConvertTypeFieldDenormalizer(new PropertyAccessor($name), $type, $emptyToNull);

        return $this->getMapping($fieldDenormalizer, $name, $policy);
    }

    public function createDateTime(
        string $name,
        bool $emptyToNull = false,
        ?\DateTimeZone $dateTimeZone = null,
        ?PolicyInterface $policy = null
    ): DenormalizationFieldMappingInterface {
        $fieldDenormalizer = new DateTimeFieldDenormalizer(
            new PropertyAccessor($name),
            $emptyToNull,
            $dateTimeZone
        );

        return $this->getMapping($fieldDenormalizer, $name, $policy);
    }

    public function createEmbedMany(
        string $name,
        string $class,
        ?PolicyInterface $policy = null
    ): DenormalizationFieldMappingInterface {
        $fieldDenormalizer = new EmbedManyFieldDenormalizer($class, new PropertyAccessor($name));

        return $this->getMapping($fieldDenormalizer, $name, $policy);
    }

    public function createEmbedOne(
        string $name,
        string $class,
        ?PolicyInterface $policy = null
    ): DenormalizationFieldMappingInterface {
        $fieldDenormalizer = new EmbedOneFieldDenormalizer($class, new PropertyAccessor($name));

        return $this->getMapping($fieldDenormalizer, $name, $policy);
    }

    public function createReferenceMany(
        string $name,
        callable $repository,
        ?PolicyInterface $policy = null
    ): DenormalizationFieldMappingInterface {
        $fieldDenormalizer = new ReferenceManyFieldDenormalizer($repository, new PropertyAccessor($name));

        return $this->getMapping($fieldDenormalizer, $name, $policy);
    }

    public function createReferenceOne(
        string $name,
        callable $repository,
        bool $emptyToNull = false,
        ?PolicyInterface $policy = null
    ): DenormalizationFieldMappingInterface {
        $fieldDenormalizer = new ReferenceOneFieldDenormalizer(
            $repository,
            new PropertyAccessor($name),
            $emptyToNull
        );

        return $this->getMapping($fieldDenormalizer, $name, $policy);
    }

    private function getMapping(
        FieldDenormalizerInterface $fieldDenormalizer,
        string $name,
        ?PolicyInterface $policy = null
    ): DenormalizationFieldMappingInterface {
        return new DenormalizationFieldMapping(
            $name,
            $fieldDenormalizer,
            $policy ?? new NullPolicy()
        );
    }
}
