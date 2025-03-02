<?php

declare(strict_types=1);

namespace Chubbyphp\Tests\Deserialization\Unit\Denormalizer\Relation;

use Chubbyphp\Deserialization\Accessor\AccessorInterface;
use Chubbyphp\Deserialization\Denormalizer\DenormalizerContextInterface;
use Chubbyphp\Deserialization\Denormalizer\Relation\ReferenceManyFieldDenormalizer;
use Chubbyphp\Deserialization\DeserializerRuntimeException;
use Chubbyphp\Mock\MockMethod\WithoutReturn;
use Chubbyphp\Mock\MockMethod\WithReturn;
use Chubbyphp\Mock\MockObjectBuilder;
use Doctrine\Common\Collections\Collection;
use PHPUnit\Framework\Attributes\DoesNotPerformAssertions;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Chubbyphp\Deserialization\Denormalizer\Relation\ReferenceManyFieldDenormalizer
 *
 * @internal
 */
final class ReferenceManyFieldDenormalizerTest extends TestCase
{
    public function testDenormalizeFieldWithoutArrayDenormalizer(): void
    {
        $this->expectException(DeserializerRuntimeException::class);
        $this->expectExceptionMessage('There is an invalid data type "double", needed "array" at path: "children"');

        $parent = new \stdClass();
        $builder = new MockObjectBuilder();

        /** @var AccessorInterface $accessor */
        $accessor = $builder->create(AccessorInterface::class, []);

        /** @var DenormalizerContextInterface $context */
        $context = $builder->create(DenormalizerContextInterface::class, []);

        $fieldDenormalizer = new ReferenceManyFieldDenormalizer(static function (string $id): void {}, $accessor);
        $fieldDenormalizer->denormalizeField('children', $parent, 18.9, $context);
    }

    public function testDenormalizeFieldWithArrayButNullChildDenormalizer(): void
    {
        $this->expectException(DeserializerRuntimeException::class);
        $this->expectExceptionMessage('There is an invalid data type "double", needed "string" at path: "children[0]"');

        $parent = new \stdClass();
        $builder = new MockObjectBuilder();

        /** @var AccessorInterface $accessor */
        $accessor = $builder->create(AccessorInterface::class, [
            new WithReturn('getValue', [$parent], []),
        ]);

        /** @var DenormalizerContextInterface $context */
        $context = $builder->create(DenormalizerContextInterface::class, []);

        $fieldDenormalizer = new ReferenceManyFieldDenormalizer(static function (string $id): void {}, $accessor);
        $fieldDenormalizer->denormalizeField('children', $parent, [18.9], $context);
    }

    #[DoesNotPerformAssertions]
    public function testDenormalizeFieldWithNull(): void
    {
        $parent = new \stdClass();
        $builder = new MockObjectBuilder();

        /** @var AccessorInterface $accessor */
        $accessor = $builder->create(AccessorInterface::class, [
            new WithReturn('getValue', [$parent], []),
            new WithoutReturn('setValue', [$parent, []]),
        ]);

        /** @var DenormalizerContextInterface $context */
        $context = $builder->create(DenormalizerContextInterface::class, []);

        $fieldDenormalizer = new ReferenceManyFieldDenormalizer(
            static function (): void {
                throw new \Exception('There should be no id to resolve');
            },
            $accessor
        );

        $fieldDenormalizer->denormalizeField('children', $parent, null, $context);
    }

    public function testDenormalizeFieldWithNewChild(): void
    {
        $parent = new \stdClass();

        $child = new \stdClass();
        $builder = new MockObjectBuilder();

        /** @var AccessorInterface $accessor */
        $accessor = $builder->create(AccessorInterface::class, [
            new WithReturn('getValue', [$parent], []),
            new WithoutReturn('setValue', [$parent, [$child]]),
        ]);

        /** @var DenormalizerContextInterface $context */
        $context = $builder->create(DenormalizerContextInterface::class, []);

        $fieldDenormalizer = new ReferenceManyFieldDenormalizer(
            static function (string $id) use ($child) {
                self::assertSame('60a9ee14-64d6-4992-8042-8d1528ac02d6', $id);

                return $child;
            },
            $accessor
        );

        $fieldDenormalizer->denormalizeField('children', $parent, ['60a9ee14-64d6-4992-8042-8d1528ac02d6'], $context);
    }

    public function testDenormalizeFieldWithNewChildAndNotFoundValue(): void
    {
        $parent = new \stdClass();
        $builder = new MockObjectBuilder();

        /** @var AccessorInterface $accessor */
        $accessor = $builder->create(AccessorInterface::class, [
            new WithReturn('getValue', [$parent], []),
            new WithoutReturn('setValue', [$parent, ['60a9ee14-64d6-4992-8042-8d1528ac02d6']]),
        ]);

        /** @var DenormalizerContextInterface $context */
        $context = $builder->create(DenormalizerContextInterface::class, []);

        $fieldDenormalizer = new ReferenceManyFieldDenormalizer(
            static function (string $id): void {
                self::assertSame('60a9ee14-64d6-4992-8042-8d1528ac02d6', $id);
            },
            $accessor
        );

        $fieldDenormalizer->denormalizeField('children', $parent, ['60a9ee14-64d6-4992-8042-8d1528ac02d6'], $context);
    }

    public function testDenormalizeFieldWithExistingChild(): void
    {
        $parent = new \stdClass();

        $child = new \stdClass();
        $builder = new MockObjectBuilder();

        /** @var AccessorInterface $accessor */
        $accessor = $builder->create(AccessorInterface::class, [
            new WithReturn('getValue', [$parent], [$child]),
            new WithoutReturn('setValue', [$parent, [$child]]),
        ]);

        /** @var DenormalizerContextInterface $context */
        $context = $builder->create(DenormalizerContextInterface::class, []);

        $fieldDenormalizer = new ReferenceManyFieldDenormalizer(
            static function (string $id) use ($child) {
                self::assertSame('60a9ee14-64d6-4992-8042-8d1528ac02d6', $id);

                return $child;
            },
            $accessor
        );

        $fieldDenormalizer->denormalizeField('children', $parent, ['60a9ee14-64d6-4992-8042-8d1528ac02d6'], $context);
    }

    public function testDenormalizeFieldWithNewChildAndCollection(): void
    {
        $parent = new \stdClass();

        $child = new \stdClass();
        $builder = new MockObjectBuilder();

        /** @var \Iterator $iterator */
        $iterator = $builder->create(\Iterator::class, [
            new WithoutReturn('rewind', []),
            new WithReturn('valid', [], false),
        ]);

        /** @var Collection $collection */
        $collection = $builder->create(Collection::class, [
            new WithReturn('getIterator', [], $iterator),
            new WithoutReturn('offsetSet', [0, $child]),
        ]);

        /** @var AccessorInterface $accessor */
        $accessor = $builder->create(AccessorInterface::class, [
            new WithReturn('getValue', [$parent], $collection),
            new WithoutReturn('setValue', [$parent, $collection]),
        ]);

        /** @var DenormalizerContextInterface $context */
        $context = $builder->create(DenormalizerContextInterface::class, []);

        $fieldDenormalizer = new ReferenceManyFieldDenormalizer(
            static function (string $id) use ($child) {
                self::assertSame('60a9ee14-64d6-4992-8042-8d1528ac02d6', $id);

                return $child;
            },
            $accessor
        );

        $fieldDenormalizer->denormalizeField('children', $parent, ['60a9ee14-64d6-4992-8042-8d1528ac02d6'], $context);
    }

    public function testDenormalizeFieldWithExistingChildAndCollection(): void
    {
        $parent = new \stdClass();

        $child = new \stdClass();
        $builder = new MockObjectBuilder();

        /** @var \Iterator $iterator */
        $iterator = $builder->create(\Iterator::class, [
            new WithoutReturn('rewind', []),
            new WithReturn('valid', [], true),
            new WithReturn('current', [], $child),
            new WithReturn('key', [], 0),
            new WithoutReturn('next', []),
            new WithReturn('valid', [], false),
        ]);

        /** @var Collection $collection */
        $collection = $builder->create(Collection::class, [
            new WithReturn('getIterator', [], $iterator),
            new WithoutReturn('offsetUnset', [0]),
            new WithoutReturn('offsetSet', [0, $child]),
        ]);

        /** @var AccessorInterface $accessor */
        $accessor = $builder->create(AccessorInterface::class, [
            new WithReturn('getValue', [$parent], $collection),
            new WithoutReturn('setValue', [$parent, $collection]),
        ]);

        /** @var DenormalizerContextInterface $context */
        $context = $builder->create(DenormalizerContextInterface::class, []);

        $fieldDenormalizer = new ReferenceManyFieldDenormalizer(
            static function (string $id) use ($child) {
                self::assertSame('60a9ee14-64d6-4992-8042-8d1528ac02d6', $id);

                return $child;
            },
            $accessor
        );

        $fieldDenormalizer->denormalizeField('children', $parent, ['60a9ee14-64d6-4992-8042-8d1528ac02d6'], $context);
    }
}
