<?php

declare(strict_types=1);

namespace Chubbyphp\Tests\Deserialization\Unit\Relation\Denormalizer;

use Chubbyphp\Deserialization\Accessor\AccessorInterface;
use Chubbyphp\Deserialization\Denormalizer\DenormalizerContextInterface;
use Chubbyphp\Deserialization\Denormalizer\DenormalizerInterface;
use Chubbyphp\Deserialization\Denormalizer\Relation\EmbedOneFieldDenormalizer;
use Chubbyphp\Deserialization\DeserializerLogicException;
use Chubbyphp\Deserialization\DeserializerRuntimeException;
use Chubbyphp\Mock\MockMethod\WithoutReturn;
use Chubbyphp\Mock\MockMethod\WithReturn;
use Chubbyphp\Mock\MockObjectBuilder;
use PHPUnit\Framework\Attributes\DoesNotPerformAssertions;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Chubbyphp\Deserialization\Denormalizer\Relation\EmbedOneFieldDenormalizer
 *
 * @internal
 */
final class EmbedOneFieldDenormalizerTest extends TestCase
{
    public function testDenormalizeFieldWithMissingDenormalizer(): void
    {
        $this->expectException(DeserializerLogicException::class);
        $this->expectExceptionMessage('There is no denormalizer at path: "reference"');

        $object = new \stdClass();

        $builder = new MockObjectBuilder();

        /** @var AccessorInterface $accessor */
        $accessor = $builder->create(AccessorInterface::class, []);

        /** @var DenormalizerContextInterface $context */
        $context = $builder->create(DenormalizerContextInterface::class, []);

        $fieldDenormalizer = new EmbedOneFieldDenormalizer(\stdClass::class, $accessor);
        $fieldDenormalizer->denormalizeField('reference', $object, ['name' => 'name'], $context);
    }

    public function testDenormalizeFieldWithWrongType(): void
    {
        $this->expectException(DeserializerRuntimeException::class);
        $this->expectExceptionMessage('There is an invalid data type "string", needed "array" at path: "reference"');

        $object = new \stdClass();
        $builder = new MockObjectBuilder();

        /** @var AccessorInterface $accessor */
        $accessor = $builder->create(AccessorInterface::class, []);

        /** @var DenormalizerContextInterface $context */
        $context = $builder->create(DenormalizerContextInterface::class, []);

        /** @var DenormalizerInterface $denormalizer */
        $denormalizer = $builder->create(DenormalizerInterface::class, []);

        $fieldDenormalizer = new EmbedOneFieldDenormalizer(\stdClass::class, $accessor);
        $fieldDenormalizer->denormalizeField('reference', $object, 'test', $context, $denormalizer);
    }

    #[DoesNotPerformAssertions]
    public function testDenormalizeFieldWithNull(): void
    {
        $object = new \stdClass();

        $builder = new MockObjectBuilder();

        /** @var AccessorInterface $accessor */
        $accessor = $builder->create(AccessorInterface::class, [
            new WithoutReturn('setValue', [$object, null]),
        ]);

        /** @var DenormalizerContextInterface $context */
        $context = $builder->create(DenormalizerContextInterface::class, []);

        /** @var DenormalizerInterface $denormalizer */
        $denormalizer = $builder->create(DenormalizerInterface::class, []);

        $fieldDenormalizer = new EmbedOneFieldDenormalizer(\stdClass::class, $accessor);
        $fieldDenormalizer->denormalizeField('reference', $object, null, $context, $denormalizer);
    }

    #[DoesNotPerformAssertions]
    public function testDenormalizeField(): void
    {
        $object = new \stdClass();
        $reference = new \stdClass();

        $builder = new MockObjectBuilder();

        /** @var AccessorInterface $accessor */
        $accessor = $builder->create(AccessorInterface::class, [
            new WithReturn('getValue', [$object], null),
            new WithoutReturn('setValue', [$object, $reference]),
        ]);

        /** @var DenormalizerContextInterface $context */
        $context = $builder->create(DenormalizerContextInterface::class, []);

        /** @var DenormalizerInterface $denormalizer */
        $denormalizer = $builder->create(DenormalizerInterface::class, [
            new WithReturn(
                'denormalize',
                [\stdClass::class, ['name' => 'name'], $context, 'reference'],
                $reference
            ),
        ]);

        $fieldDenormalizer = new EmbedOneFieldDenormalizer(\stdClass::class, $accessor);
        $fieldDenormalizer->denormalizeField('reference', $object, ['name' => 'name'], $context, $denormalizer);
    }

    #[DoesNotPerformAssertions]
    public function testDenormalizeFieldWithExistingValue(): void
    {
        $object = new \stdClass();

        $reference = new \stdClass();

        $builder = new MockObjectBuilder();

        /** @var AccessorInterface $accessor */
        $accessor = $builder->create(AccessorInterface::class, [
            new WithReturn('getValue', [$object], $reference),
            new WithoutReturn('setValue', [$object, $reference]),
        ]);

        /** @var DenormalizerContextInterface $context */
        $context = $builder->create(DenormalizerContextInterface::class, []);

        /** @var DenormalizerInterface $denormalizer */
        $denormalizer = $builder->create(DenormalizerInterface::class, [
            new WithReturn(
                'denormalize',
                [$reference, ['name' => 'name'], $context, 'reference'],
                $reference
            ),
        ]);

        $fieldDenormalizer = new EmbedOneFieldDenormalizer(\stdClass::class, $accessor);
        $fieldDenormalizer->denormalizeField('reference', $object, ['name' => 'name'], $context, $denormalizer);
    }

    #[DoesNotPerformAssertions]
    public function testDenormalizeFieldWithReverseOwning(): void
    {
        $object = new \stdClass();

        $reference = new \stdClass();

        $builder = new MockObjectBuilder();

        /** @var AccessorInterface $accessor */
        $accessor = $builder->create(AccessorInterface::class, [
            new WithReturn('getValue', [$object], null),
            new WithoutReturn('setValue', [$object, $reference]),
        ]);

        /** @var DenormalizerContextInterface $context */
        $context = $builder->create(DenormalizerContextInterface::class, []);

        /** @var DenormalizerInterface $denormalizer */
        $denormalizer = $builder->create(DenormalizerInterface::class, [
            new WithReturn(
                'denormalize',
                [\stdClass::class, ['name' => 'name'], $context, 'reference'],
                $reference
            ),
        ]);

        /** @var AccessorInterface $parentAccessor */
        $parentAccessor = $builder->create(AccessorInterface::class, [
            new WithoutReturn('setValue', [$reference, $object]),
        ]);

        $fieldDenormalizer = new EmbedOneFieldDenormalizer(\stdClass::class, $accessor, $parentAccessor);
        $fieldDenormalizer->denormalizeField('reference', $object, ['name' => 'name'], $context, $denormalizer);
    }

    #[DoesNotPerformAssertions]
    public function testDenormalizeFieldWithExistingValueAndWithReverseOwning(): void
    {
        $object = new \stdClass();

        $reference = new \stdClass();

        $builder = new MockObjectBuilder();

        /** @var AccessorInterface $accessor */
        $accessor = $builder->create(AccessorInterface::class, [
            new WithReturn('getValue', [$object], $reference),
            new WithoutReturn('setValue', [$object, $reference]),
        ]);

        /** @var DenormalizerContextInterface $context */
        $context = $builder->create(DenormalizerContextInterface::class, []);

        /** @var DenormalizerInterface $denormalizer */
        $denormalizer = $builder->create(DenormalizerInterface::class, [
            new WithReturn('denormalize', [$reference, ['name' => 'name'], $context, 'reference'], $reference),
        ]);

        /** @var AccessorInterface $parentAccessor */
        $parentAccessor = $builder->create(AccessorInterface::class, [
            new WithoutReturn('setValue', [$reference, $object]),
        ]);

        $fieldDenormalizer = new EmbedOneFieldDenormalizer(\stdClass::class, $accessor, $parentAccessor);
        $fieldDenormalizer->denormalizeField('reference', $object, ['name' => 'name'], $context, $denormalizer);
    }
}
