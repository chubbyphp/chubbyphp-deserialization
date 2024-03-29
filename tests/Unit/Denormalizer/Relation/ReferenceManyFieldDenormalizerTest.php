<?php

declare(strict_types=1);

namespace Chubbyphp\Tests\Deserialization\Unit\Denormalizer\Relation;

use Chubbyphp\Deserialization\Accessor\AccessorInterface;
use Chubbyphp\Deserialization\Denormalizer\DenormalizerContextInterface;
use Chubbyphp\Deserialization\Denormalizer\Relation\ReferenceManyFieldDenormalizer;
use Chubbyphp\Deserialization\DeserializerRuntimeException;
use Chubbyphp\Mock\Call;
use Chubbyphp\Mock\MockByCallsTrait;
use Doctrine\Common\Collections\Collection;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Chubbyphp\Deserialization\Denormalizer\Relation\ReferenceManyFieldDenormalizer
 *
 * @internal
 */
final class ReferenceManyFieldDenormalizerTest extends TestCase
{
    use MockByCallsTrait;

    public function testDenormalizeFieldWithoutArrayDenormalizer(): void
    {
        $this->expectException(DeserializerRuntimeException::class);
        $this->expectExceptionMessage('There is an invalid data type "double", needed "array" at path: "children"');

        $parent = new \stdClass();

        /** @var AccessorInterface|MockObject $accessor */
        $accessor = $this->getMockByCalls(AccessorInterface::class);

        /** @var DenormalizerContextInterface|MockObject $context */
        $context = $this->getMockByCalls(DenormalizerContextInterface::class);

        $fieldDenormalizer = new ReferenceManyFieldDenormalizer(static function (string $id): void {}, $accessor);
        $fieldDenormalizer->denormalizeField('children', $parent, 18.9, $context);
    }

    public function testDenormalizeFieldWithArrayButNullChildDenormalizer(): void
    {
        $this->expectException(DeserializerRuntimeException::class);
        $this->expectExceptionMessage('There is an invalid data type "double", needed "string" at path: "children[0]"');

        $parent = new \stdClass();

        /** @var AccessorInterface|MockObject $accessor */
        $accessor = $this->getMockByCalls(AccessorInterface::class, [
            Call::create('getValue')->with($parent)->willReturn([]),
        ]);

        /** @var DenormalizerContextInterface|MockObject $context */
        $context = $this->getMockByCalls(DenormalizerContextInterface::class);

        $fieldDenormalizer = new ReferenceManyFieldDenormalizer(static function (string $id): void {}, $accessor);
        $fieldDenormalizer->denormalizeField('children', $parent, [18.9], $context);
    }

    public function testDenormalizeFieldWithNull(): void
    {
        $parent = new \stdClass();

        /** @var AccessorInterface|MockObject $accessor */
        $accessor = $this->getMockByCalls(AccessorInterface::class, [
            Call::create('getValue')->with($parent)->willReturn([]),
            Call::create('setValue')->with($parent, []),
        ]);

        /** @var DenormalizerContextInterface|MockObject $context */
        $context = $this->getMockByCalls(DenormalizerContextInterface::class);

        $fieldDenormalizer = new ReferenceManyFieldDenormalizer(
            static function (): void {
                self::fail('There should be no id to resolve');
            },
            $accessor
        );

        $fieldDenormalizer->denormalizeField('children', $parent, null, $context);
    }

    public function testDenormalizeFieldWithNewChild(): void
    {
        $parent = new \stdClass();

        $child = new \stdClass();

        /** @var AccessorInterface|MockObject $accessor */
        $accessor = $this->getMockByCalls(AccessorInterface::class, [
            Call::create('getValue')->with($parent)->willReturn([]),
            Call::create('setValue')->with($parent, [$child]),
        ]);

        /** @var DenormalizerContextInterface|MockObject $context */
        $context = $this->getMockByCalls(DenormalizerContextInterface::class);

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

        /** @var AccessorInterface|MockObject $accessor */
        $accessor = $this->getMockByCalls(AccessorInterface::class, [
            Call::create('getValue')->with($parent)->willReturn([]),
            Call::create('setValue')->with($parent, ['60a9ee14-64d6-4992-8042-8d1528ac02d6']),
        ]);

        /** @var DenormalizerContextInterface|MockObject $context */
        $context = $this->getMockByCalls(DenormalizerContextInterface::class);

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

        /** @var AccessorInterface|MockObject $accessor */
        $accessor = $this->getMockByCalls(AccessorInterface::class, [
            Call::create('getValue')->with($parent)->willReturn([$child]),
            Call::create('setValue')->with($parent, [$child]),
        ]);

        /** @var DenormalizerContextInterface|MockObject $context */
        $context = $this->getMockByCalls(DenormalizerContextInterface::class);

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

        /** @var \Iterator|MockObject $iterator */
        $iterator = $this->getMockByCalls(\Iterator::class, [
            Call::create('rewind')->with(),
            Call::create('valid')->with()->willReturn(false),
        ]);

        /** @var Collection|MockObject $collection */
        $collection = $this->getMockByCalls(Collection::class, [
            Call::create('getIterator')->with()->willReturn($iterator),
            Call::create('offsetSet')->with(0, $child),
        ]);

        /** @var AccessorInterface|MockObject $accessor */
        $accessor = $this->getMockByCalls(AccessorInterface::class, [
            Call::create('getValue')->with($parent)->willReturn($collection),
            Call::create('setValue')->with($parent, $collection),
        ]);

        /** @var DenormalizerContextInterface|MockObject $context */
        $context = $this->getMockByCalls(DenormalizerContextInterface::class);

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

        /** @var \Iterator|MockObject $iterator */
        $iterator = $this->getMockByCalls(\Iterator::class, [
            Call::create('rewind')->with(),
            Call::create('valid')->with()->willReturn(true),
            Call::create('current')->with()->willReturn($child),
            Call::create('key')->with()->willReturn(0),
            Call::create('next')->with(),
            Call::create('valid')->with()->willReturn(false),
        ]);

        /** @var Collection|MockObject $collection */
        $collection = $this->getMockByCalls(Collection::class, [
            Call::create('getIterator')->with()->willReturn($iterator),
            Call::create('offsetUnset')->with(0),
            Call::create('offsetSet')->with(0, $child),
        ]);

        /** @var AccessorInterface|MockObject $accessor */
        $accessor = $this->getMockByCalls(AccessorInterface::class, [
            Call::create('getValue')->with($parent)->willReturn($collection),
            Call::create('setValue')->with($parent, $collection),
        ]);

        /** @var DenormalizerContextInterface|MockObject $context */
        $context = $this->getMockByCalls(DenormalizerContextInterface::class);

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
