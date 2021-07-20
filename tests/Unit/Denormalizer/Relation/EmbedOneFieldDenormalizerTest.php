<?php

declare(strict_types=1);

namespace Chubbyphp\Tests\Deserialization\Unit\Relation\Denormalizer;

use Chubbyphp\Deserialization\Accessor\AccessorInterface;
use Chubbyphp\Deserialization\Denormalizer\DenormalizerContextInterface;
use Chubbyphp\Deserialization\Denormalizer\DenormalizerInterface;
use Chubbyphp\Deserialization\Denormalizer\Relation\EmbedOneFieldDenormalizer;
use Chubbyphp\Deserialization\DeserializerLogicException;
use Chubbyphp\Deserialization\DeserializerRuntimeException;
use Chubbyphp\Mock\Call;
use Chubbyphp\Mock\MockByCallsTrait;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Chubbyphp\Deserialization\Denormalizer\Relation\EmbedOneFieldDenormalizer
 *
 * @internal
 */
final class EmbedOneFieldDenormalizerTest extends TestCase
{
    use MockByCallsTrait;

    public function testDenormalizeFieldWithMissingDenormalizer(): void
    {
        $this->expectException(DeserializerLogicException::class);
        $this->expectExceptionMessage('There is no denormalizer at path: "reference"');

        $object = new \stdClass();

        /** @var AccessorInterface|MockObject $accessor */
        $accessor = $this->getMockByCalls(AccessorInterface::class);

        /** @var DenormalizerContextInterface|MockObject $context */
        $context = $this->getMockByCalls(DenormalizerContextInterface::class);

        $fieldDenormalizer = new EmbedOneFieldDenormalizer(\stdClass::class, $accessor);
        $fieldDenormalizer->denormalizeField('reference', $object, ['name' => 'name'], $context);
    }

    public function testDenormalizeFieldWithWrongType(): void
    {
        $this->expectException(DeserializerRuntimeException::class);
        $this->expectExceptionMessage('There is an invalid data type "string", needed "array" at path: "reference"');

        $object = new \stdClass();

        /** @var AccessorInterface|MockObject $accessor */
        $accessor = $this->getMockByCalls(AccessorInterface::class);

        /** @var DenormalizerContextInterface|MockObject $context */
        $context = $this->getMockByCalls(DenormalizerContextInterface::class);

        /** @var DenormalizerInterface|MockObject $denormalizer */
        $denormalizer = $this->getMockByCalls(DenormalizerInterface::class);

        $fieldDenormalizer = new EmbedOneFieldDenormalizer(\stdClass::class, $accessor);
        $fieldDenormalizer->denormalizeField('reference', $object, 'test', $context, $denormalizer);
    }

    public function testDenormalizeFieldWithNull(): void
    {
        $object = new \stdClass();

        /** @var AccessorInterface|MockObject $accessor */
        $accessor = $this->getMockByCalls(AccessorInterface::class, [
            Call::create('setValue')->with($object, null),
        ]);

        /** @var DenormalizerContextInterface|MockObject $context */
        $context = $this->getMockByCalls(DenormalizerContextInterface::class);

        /** @var DenormalizerInterface|MockObject $denormalizer */
        $denormalizer = $this->getMockByCalls(DenormalizerInterface::class);

        $fieldDenormalizer = new EmbedOneFieldDenormalizer(\stdClass::class, $accessor);
        $fieldDenormalizer->denormalizeField('reference', $object, null, $context, $denormalizer);
    }

    public function testDenormalizeField(): void
    {
        $object = new \stdClass();

        $reference = new \stdClass();

        /** @var AccessorInterface|MockObject $accessor */
        $accessor = $this->getMockByCalls(AccessorInterface::class, [
            Call::create('getValue')->with($object)->willReturn(null),
            Call::create('setValue')->with($object, $reference),
        ]);

        /** @var DenormalizerContextInterface|MockObject $context */
        $context = $this->getMockByCalls(DenormalizerContextInterface::class);

        /** @var DenormalizerInterface|MockObject $denormalizer */
        $denormalizer = $this->getMockByCalls(DenormalizerInterface::class, [
            Call::create('denormalize')
                ->with(\stdClass::class, ['name' => 'name'], $context, 'reference')
                ->willReturn($reference),
        ]);

        $fieldDenormalizer = new EmbedOneFieldDenormalizer(\stdClass::class, $accessor);
        $fieldDenormalizer->denormalizeField('reference', $object, ['name' => 'name'], $context, $denormalizer);
    }

    public function testDenormalizeFieldWithExistingValue(): void
    {
        $object = new \stdClass();

        $reference = new \stdClass();

        /** @var AccessorInterface|MockObject $accessor */
        $accessor = $this->getMockByCalls(AccessorInterface::class, [
            Call::create('getValue')->with($object)->willReturn($reference),
            Call::create('setValue')->with($object, $reference),
        ]);

        /** @var DenormalizerContextInterface|MockObject $context */
        $context = $this->getMockByCalls(DenormalizerContextInterface::class);

        /** @var DenormalizerInterface|MockObject $denormalizer */
        $denormalizer = $this->getMockByCalls(DenormalizerInterface::class, [
            Call::create('denormalize')
                ->with($reference, ['name' => 'name'], $context, 'reference')
                ->willReturn($reference),
        ]);

        $fieldDenormalizer = new EmbedOneFieldDenormalizer(\stdClass::class, $accessor);
        $fieldDenormalizer->denormalizeField('reference', $object, ['name' => 'name'], $context, $denormalizer);
    }

    public function testDenormalizeFieldWithMissingParentAccessor(): void
    {
        $this->expectException(DeserializerLogicException::class);
        $this->expectExceptionMessage('There is no parent accessor at path: "reference"');

        $object = new \stdClass();

        $reference = new \stdClass();

        /** @var AccessorInterface|MockObject $accessor */
        $accessor = $this->getMockByCalls(AccessorInterface::class, [
            Call::create('getValue')->with($object)->willReturn(null),
            Call::create('setValue')->with($object, $reference),
        ]);

        /** @var DenormalizerContextInterface|MockObject $context */
        $context = $this->getMockByCalls(DenormalizerContextInterface::class);

        /** @var DenormalizerInterface|MockObject $denormalizer */
        $denormalizer = $this->getMockByCalls(DenormalizerInterface::class, [
            Call::create('denormalize')
                ->with(\stdClass::class, ['name' => 'name'], $context, 'reference')
                ->willReturn($reference),
        ]);

        $fieldDenormalizer = new EmbedOneFieldDenormalizer(\stdClass::class, $accessor);
        $fieldDenormalizer->denormalizeField('reference', $object, ['name' => 'name'], $context, $denormalizer, true);
    }

    public function testDenormalizeFieldWithReverseOwning(): void
    {
        $object = new \stdClass();

        $reference = new \stdClass();

        /** @var AccessorInterface|MockObject $accessor */
        $accessor = $this->getMockByCalls(AccessorInterface::class, [
            Call::create('getValue')->with($object)->willReturn(null),
            Call::create('setValue')->with($object, $reference),
        ]);

        /** @var DenormalizerContextInterface|MockObject $context */
        $context = $this->getMockByCalls(DenormalizerContextInterface::class);

        /** @var DenormalizerInterface|MockObject $denormalizer */
        $denormalizer = $this->getMockByCalls(DenormalizerInterface::class, [
            Call::create('denormalize')
                ->with(\stdClass::class, ['name' => 'name'], $context, 'reference')
                ->willReturn($reference),
        ]);

        /** @var AccessorInterface|MockObject $parentAccessor */
        $parentAccessor = $this->getMockByCalls(AccessorInterface::class, [
            Call::create('setValue')->with($reference, $object),
        ]);

        $fieldDenormalizer = new EmbedOneFieldDenormalizer(\stdClass::class, $accessor, $parentAccessor);
        $fieldDenormalizer->denormalizeField('reference', $object, ['name' => 'name'], $context, $denormalizer, true);
    }

    public function testDenormalizeFieldWithExistingValueAndWithReverseOwning(): void
    {
        $object = new \stdClass();

        $reference = new \stdClass();

        /** @var AccessorInterface|MockObject $accessor */
        $accessor = $this->getMockByCalls(AccessorInterface::class, [
            Call::create('getValue')->with($object)->willReturn($reference),
            Call::create('setValue')->with($object, $reference),
        ]);

        /** @var DenormalizerContextInterface|MockObject $context */
        $context = $this->getMockByCalls(DenormalizerContextInterface::class);

        /** @var DenormalizerInterface|MockObject $denormalizer */
        $denormalizer = $this->getMockByCalls(DenormalizerInterface::class, [
            Call::create('denormalize')
                ->with($reference, ['name' => 'name'], $context, 'reference')
                ->willReturn($reference),
        ]);

        /** @var AccessorInterface|MockObject $parentAccessor */
        $parentAccessor = $this->getMockByCalls(AccessorInterface::class, [
            Call::create('setValue')->with($reference, $object),
        ]);

        $fieldDenormalizer = new EmbedOneFieldDenormalizer(\stdClass::class, $accessor, $parentAccessor);
        $fieldDenormalizer->denormalizeField('reference', $object, ['name' => 'name'], $context, $denormalizer, true);
    }
}
