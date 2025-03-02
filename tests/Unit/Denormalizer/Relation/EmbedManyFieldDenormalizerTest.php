<?php

declare(strict_types=1);

namespace Chubbyphp\Tests\Deserialization\Unit\Denormalizer\Relation;

use Chubbyphp\Deserialization\Accessor\AccessorInterface;
use Chubbyphp\Deserialization\Denormalizer\DenormalizerContextInterface;
use Chubbyphp\Deserialization\Denormalizer\DenormalizerInterface;
use Chubbyphp\Deserialization\Denormalizer\Relation\EmbedManyFieldDenormalizer;
use Chubbyphp\Deserialization\DeserializerLogicException;
use Chubbyphp\Deserialization\DeserializerRuntimeException;
use Chubbyphp\Mock\MockMethod\WithoutReturn;
use Chubbyphp\Mock\MockMethod\WithReturn;
use Chubbyphp\Mock\MockObjectBuilder;
use Doctrine\Common\Collections\Collection;
use PHPUnit\Framework\Attributes\DoesNotPerformAssertions;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Chubbyphp\Deserialization\Denormalizer\Relation\EmbedManyFieldDenormalizer
 *
 * @internal
 */
final class EmbedManyFieldDenormalizerTest extends TestCase
{
    public function testDenormalizeFieldWithMissingDenormalizer(): void
    {
        $this->expectException(DeserializerLogicException::class);
        $this->expectExceptionMessage('There is no denormalizer at path: "children"');

        $parent = new \stdClass();

        $builder = new MockObjectBuilder();

        /** @var AccessorInterface $accessor */
        $accessor = $builder->create(AccessorInterface::class, []);

        /** @var DenormalizerContextInterface $context */
        $context = $builder->create(DenormalizerContextInterface::class, []);

        $fieldDenormalizer = new EmbedManyFieldDenormalizer(\stdClass::class, $accessor);
        $fieldDenormalizer->denormalizeField('children', $parent, [['name' => 'name']], $context);
    }

    public function testDenormalizeFieldWithoutArrayDenormalizer(): void
    {
        $this->expectException(DeserializerRuntimeException::class);
        $this->expectExceptionMessage('There is an invalid data type "string", needed "array" at path: "children"');

        $parent = new \stdClass();

        $builder = new MockObjectBuilder();

        /** @var AccessorInterface $accessor */
        $accessor = $builder->create(AccessorInterface::class, []);

        /** @var DenormalizerContextInterface $context */
        $context = $builder->create(DenormalizerContextInterface::class, []);

        /** @var DenormalizerInterface $denormalizer */
        $denormalizer = $builder->create(DenormalizerInterface::class, []);

        $fieldDenormalizer = new EmbedManyFieldDenormalizer(\stdClass::class, $accessor);
        $fieldDenormalizer->denormalizeField('children', $parent, 'test', $context, $denormalizer);
    }

    public function testDenormalizeFieldWithArrayButStringChildDenormalizer(): void
    {
        $this->expectException(DeserializerRuntimeException::class);
        $this->expectExceptionMessage('There is an invalid data type "string", needed "array" at path: "children[0]"');

        $parent = new \stdClass();

        $builder = new MockObjectBuilder();

        /** @var AccessorInterface $accessor */
        $accessor = $builder->create(AccessorInterface::class, [
            new WithReturn('getValue', [$parent], []),
        ]);

        /** @var DenormalizerContextInterface $context */
        $context = $builder->create(DenormalizerContextInterface::class, []);

        /** @var DenormalizerInterface $denormalizer */
        $denormalizer = $builder->create(DenormalizerInterface::class, []);

        $fieldDenormalizer = new EmbedManyFieldDenormalizer(\stdClass::class, $accessor);
        $fieldDenormalizer->denormalizeField('children', $parent, ['test'], $context, $denormalizer);
    }

    #[DoesNotPerformAssertions]
    public function testDenormalizeFieldWithNull(): void
    {
        $parent = new \stdClass();

        $builder = new MockObjectBuilder();

        $accessor = $builder->create(AccessorInterface::class, [
            new WithReturn('getValue', [$parent], []),
            new WithoutReturn('setValue', [$parent, []]),
        ]);

        /** @var DenormalizerContextInterface $context */
        $context = $builder->create(DenormalizerContextInterface::class, []);

        /** @var DenormalizerInterface $denormalizer */
        $denormalizer = $builder->create(DenormalizerInterface::class, []);

        $fieldDenormalizer = new EmbedManyFieldDenormalizer(\stdClass::class, $accessor);
        $fieldDenormalizer->denormalizeField('children', $parent, null, $context, $denormalizer);
    }

    #[DoesNotPerformAssertions]
    public function testDenormalizeFieldWithSubValueNull(): void
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

        /** @var DenormalizerInterface $denormalizer */
        $denormalizer = $builder->create(DenormalizerInterface::class, [
            new WithReturn(
                'denormalize',
                [\stdClass::class, [], $context, 'children[0]'],
                $child
            ),
        ]);

        $fieldDenormalizer = new EmbedManyFieldDenormalizer(\stdClass::class, $accessor);
        $fieldDenormalizer->denormalizeField('children', $parent, [null], $context, $denormalizer);
    }

    #[DoesNotPerformAssertions]
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

        /** @var DenormalizerInterface $denormalizer */
        $denormalizer = $builder->create(DenormalizerInterface::class, [
            new WithReturn(
                'denormalize',
                [\stdClass::class, ['name' => 'name'], $context, 'children[0]'],
                $child
            ),
        ]);

        $fieldDenormalizer = new EmbedManyFieldDenormalizer(\stdClass::class, $accessor);
        $fieldDenormalizer->denormalizeField('children', $parent, [['name' => 'name']], $context, $denormalizer);
    }

    #[DoesNotPerformAssertions]
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

        /** @var DenormalizerInterface $denormalizer */
        $denormalizer = $builder->create(DenormalizerInterface::class, [
            new WithReturn(
                'denormalize',
                [\stdClass::class, ['name' => 'name'], $context, 'children[0]'],
                $child
            ),
        ]);

        $fieldDenormalizer = new EmbedManyFieldDenormalizer(\stdClass::class, $accessor);
        $fieldDenormalizer->denormalizeField('children', $parent, [['name' => 'name']], $context, $denormalizer);
    }

    #[DoesNotPerformAssertions]
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

        /** @var DenormalizerInterface $denormalizer */
        $denormalizer = $builder->create(DenormalizerInterface::class, [
            new WithReturn('denormalize', [$child, ['name' => 'name'], $context, 'children[0]'], $child),
        ]);

        $fieldDenormalizer = new EmbedManyFieldDenormalizer(\stdClass::class, $accessor);
        $fieldDenormalizer->denormalizeField('children', $parent, [['name' => 'name']], $context, $denormalizer);
    }

    #[DoesNotPerformAssertions]
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

        /** @var DenormalizerInterface $denormalizer */
        $denormalizer = $builder->create(DenormalizerInterface::class, [
            new WithReturn('denormalize', [$child, ['name' => 'name'], $context, 'children[0]'], $child),
        ]);

        $fieldDenormalizer = new EmbedManyFieldDenormalizer(\stdClass::class, $accessor);
        $fieldDenormalizer->denormalizeField('children', $parent, [['name' => 'name']], $context, $denormalizer);
    }
}
