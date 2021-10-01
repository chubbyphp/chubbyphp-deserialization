<?php

declare(strict_types=1);

namespace Chubbyphp\Deserialization\Mapping;

use Chubbyphp\Deserialization\Denormalizer\FieldDenormalizerInterface;
use Chubbyphp\Deserialization\Policy\PolicyInterface;

/**
 * @method DenormalizationFieldMappingInterface createDateTimeImmutable(string $name, bool $emptyToNull = false, ?\DateTimeZone $dateTimeZone = null, ?PolicyInterface $policy = null)
 */
interface DenormalizationFieldMappingFactoryInterface
{
    public function create(
        string $name,
        bool $emptyToNull = false,
        ?FieldDenormalizerInterface $fieldDenormalizer = null,
        ?PolicyInterface $policy = null
    ): DenormalizationFieldMappingInterface;

    public function createCallback(
        string $name,
        callable $callback,
        ?PolicyInterface $policy = null
    ): DenormalizationFieldMappingInterface;

    public function createConvertType(
        string $name,
        string $type,
        bool $emptyToNull = false,
        ?PolicyInterface $policy = null
    ): DenormalizationFieldMappingInterface;

    /**
     * @deprecated Use createDateTimeImmutable instead
     */
    public function createDateTime(
        string $name,
        bool $emptyToNull = false,
        ?\DateTimeZone $dateTimeZone = null,
        ?PolicyInterface $policy = null
    ): DenormalizationFieldMappingInterface;

    // public function createDateTimeImmutable(
    //     string $name,
    //     bool $emptyToNull = false,
    //     ?\DateTimeZone $dateTimeZone = null,
    //     ?PolicyInterface $policy = null
    // ): DenormalizationFieldMappingInterface;

    public function createEmbedMany(
        string $name,
        string $class,
        ?PolicyInterface $policy = null
    ): DenormalizationFieldMappingInterface;

    public function createEmbedOne(
        string $name,
        string $class,
        ?PolicyInterface $policy = null
    ): DenormalizationFieldMappingInterface;

    public function createReferenceMany(
        string $name,
        callable $repository,
        ?PolicyInterface $policy = null
    ): DenormalizationFieldMappingInterface;

    public function createReferenceOne(
        string $name,
        callable $repository,
        bool $emptyToNull = false,
        ?PolicyInterface $policy = null
    ): DenormalizationFieldMappingInterface;
}
