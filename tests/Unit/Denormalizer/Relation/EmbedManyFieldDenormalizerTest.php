<?php

declare(strict_types=1);

namespace Chubbyphp\Tests\Deserialization\Unit\Denormalizer\Relation;

use Chubbyphp\Deserialization\Accessor\AccessorInterface;
use Chubbyphp\Deserialization\Denormalizer\DenormalizerContextInterface;
use Chubbyphp\Deserialization\Denormalizer\DenormalizerInterface;
use Chubbyphp\Deserialization\Denormalizer\Relation\EmbedManyFieldDenormalizer;
use Chubbyphp\Deserialization\DeserializerLogicException;
use Chubbyphp\Deserialization\DeserializerRuntimeException;
use Chubbyphp\Mock\Call;
use Chubbyphp\Mock\MockByCallsTrait;
use Doctrine\Common\Collections\Collection;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Chubbyphp\Deserialization\Denormalizer\Relation\EmbedManyFieldDenormalizer
 *
 * @internal
 */
final class EmbedManyFieldDenormalizerTest extends TestCase
{
    use MockByCallsTrait;

    public function testDenormalizeFieldWithMissingDenormalizer(): void
    {
        $this->expectException(DeserializerLogicException::class);
        $this->expectExceptionMessage('There is no denormalizer at path: "children"');

        $parent = new \stdClass();

        /** @var AccessorInterface|MockObject $accessor */
        $accessor = $this->getMockByCalls(AccessorInterface::class);

        /** @var DenormalizerContextInterface|MockObject $context */
        $context = $this->getMockByCalls(DenormalizerContextInterface::class);

        $fieldDenormalizer = new EmbedManyFieldDenormalizer(\stdClass::class, $accessor);
        $fieldDenormalizer->denormalizeField('children', $parent, [['name' => 'name']], $context);
    }

    public function testDenormalizeFieldWithoutArrayDenormalizer(): void
    {
        $this->expectException(DeserializerRuntimeException::class);
        $this->expectExceptionMessage('There is an invalid data type "string", needed "array" at path: "children"');

        $parent = new \stdClass();

        /** @var AccessorInterface|MockObject $accessor */
        $accessor = $this->getMockByCalls(AccessorInterface::class);

        /** @var DenormalizerContextInterface|MockObject $context */
        $context = $this->getMockByCalls(DenormalizerContextInterface::class);

        /** @var DenormalizerInterface|MockObject $denormalizer */
        $denormalizer = $this->getMockByCalls(DenormalizerInterface::class);

        $fieldDenormalizer = new EmbedManyFieldDenormalizer(\stdClass::class, $accessor);
        $fieldDenormalizer->denormalizeField('children', $parent, 'test', $context, $denormalizer);
    }

    public function testDenormalizeFieldWithArrayButStringChildDenormalizer(): void
    {
        $this->expectException(DeserializerRuntimeException::class);
        $this->expectExceptionMessage('There is an invalid data type "string", needed "array" at path: "children[0]"');

        $parent = new \stdClass();

        /** @var AccessorInterface|MockObject $accessor */
        $accessor = $this->getMockByCalls(AccessorInterface::class, [
            Call::create('getValue')->with($parent)->willReturn([]),
        ]);

        /** @var DenormalizerContextInterface|MockObject $context */
        $context = $this->getMockByCalls(DenormalizerContextInterface::class);

        /** @var DenormalizerInterface|MockObject $denormalizer */
        $denormalizer = $this->getMockByCalls(DenormalizerInterface::class);

        $fieldDenormalizer = new EmbedManyFieldDenormalizer(\stdClass::class, $accessor);
        $fieldDenormalizer->denormalizeField('children', $parent, ['test'], $context, $denormalizer);
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

        /** @var DenormalizerInterface|MockObject $denormalizer */
        $denormalizer = $this->getMockByCalls(DenormalizerInterface::class);

        $fieldDenormalizer = new EmbedManyFieldDenormalizer(\stdClass::class, $accessor);
        $fieldDenormalizer->denormalizeField('children', $parent, null, $context, $denormalizer);
    }

    public function testDenormalizeFieldWithSubValueNull(): void
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

        /** @var DenormalizerInterface|MockObject $denormalizer */
        $denormalizer = $this->getMockByCalls(DenormalizerInterface::class, [
            Call::create('denormalize')
                ->with(\stdClass::class, [], $context, 'children[0]')
                ->willReturn($child),
        ]);

        $fieldDenormalizer = new EmbedManyFieldDenormalizer(\stdClass::class, $accessor);
        $fieldDenormalizer->denormalizeField('children', $parent, [null], $context, $denormalizer);
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

        /** @var DenormalizerInterface|MockObject $denormalizer */
        $denormalizer = $this->getMockByCalls(DenormalizerInterface::class, [
            Call::create('denormalize')
                ->with(\stdClass::class, ['name' => 'name'], $context, 'children[0]')
                ->willReturn($child),
        ]);

        $fieldDenormalizer = new EmbedManyFieldDenormalizer(\stdClass::class, $accessor);
        $fieldDenormalizer->denormalizeField('children', $parent, [['name' => 'name']], $context, $denormalizer);
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

        /** @var DenormalizerInterface|MockObject $denormalizer */
        $denormalizer = $this->getMockByCalls(DenormalizerInterface::class, [
            Call::create('denormalize')
                ->with(\stdClass::class, ['name' => 'name'], $context, 'children[0]')
                ->willReturn($child),
        ]);

        $fieldDenormalizer = new EmbedManyFieldDenormalizer(\stdClass::class, $accessor);
        $fieldDenormalizer->denormalizeField('children', $parent, [['name' => 'name']], $context, $denormalizer);
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

        /** @var DenormalizerInterface|MockObject $denormalizer */
        $denormalizer = $this->getMockByCalls(DenormalizerInterface::class, [
            Call::create('denormalize')
                ->with($child, ['name' => 'name'], $context, 'children[0]')
                ->willReturn($child),
        ]);

        $fieldDenormalizer = new EmbedManyFieldDenormalizer(\stdClass::class, $accessor);
        $fieldDenormalizer->denormalizeField('children', $parent, [['name' => 'name']], $context, $denormalizer);
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

        /** @var DenormalizerInterface|MockObject $denormalizer */
        $denormalizer = $this->getMockByCalls(DenormalizerInterface::class, [
            Call::create('denormalize')
                ->with($child, ['name' => 'name'], $context, 'children[0]')
                ->willReturn($child),
        ]);

        $fieldDenormalizer = new EmbedManyFieldDenormalizer(\stdClass::class, $accessor);
        $fieldDenormalizer->denormalizeField('children', $parent, [['name' => 'name']], $context, $denormalizer);
    }
}
