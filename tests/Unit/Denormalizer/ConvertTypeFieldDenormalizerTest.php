<?php

declare(strict_types=1);

namespace Chubbyphp\Tests\Deserialization\Unit\Denormalizer;

use Chubbyphp\Deserialization\Accessor\AccessorInterface;
use Chubbyphp\Deserialization\Denormalizer\ConvertTypeFieldDenormalizer;
use Chubbyphp\Deserialization\Denormalizer\DenormalizerContextInterface;
use Chubbyphp\Deserialization\DeserializerLogicException;
use Chubbyphp\Mock\Call;
use Chubbyphp\Mock\MockByCallsTrait;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Chubbyphp\Deserialization\Denormalizer\ConvertTypeFieldDenormalizer
 *
 * @internal
 */
final class ConvertTypeFieldDenormalizerTest extends TestCase
{
    use MockByCallsTrait;

    public function testDenormalizeFieldWithInvalidType(): void
    {
        $this->expectException(DeserializerLogicException::class);
        $this->expectExceptionMessage('Convert type "type" is not supported');

        /** @var AccessorInterface|MockObject $accessor */
        $accessor = $this->getMockByCalls(AccessorInterface::class);

        new ConvertTypeFieldDenormalizer($accessor, 'type');
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

        $fieldDenormalizer = new ConvertTypeFieldDenormalizer($accessor, ConvertTypeFieldDenormalizer::TYPE_INT);
        $fieldDenormalizer->denormalizeField('value', $object, null, $context);
    }

    public function testDenormalizeFieldWithArray(): void
    {
        $object = new \stdClass();

        /** @var AccessorInterface|MockObject $accessor */
        $accessor = $this->getMockByCalls(AccessorInterface::class, [
            Call::create('setValue')->with($object, []),
        ]);

        /** @var DenormalizerContextInterface|MockObject $context */
        $context = $this->getMockByCalls(DenormalizerContextInterface::class);

        $fieldDenormalizer = new ConvertTypeFieldDenormalizer($accessor, ConvertTypeFieldDenormalizer::TYPE_INT);
        $fieldDenormalizer->denormalizeField('value', $object, [], $context);
    }

    public function testDenormalizeFieldWithFloatWhichCantBeConvertedToBool(): void
    {
        $object = new \stdClass();

        /** @var AccessorInterface|MockObject $accessor */
        $accessor = $this->getMockByCalls(AccessorInterface::class, [
            Call::create('setValue')->with($object, 1.0),
        ]);

        /** @var DenormalizerContextInterface|MockObject $context */
        $context = $this->getMockByCalls(DenormalizerContextInterface::class);

        $fieldDenormalizer = new ConvertTypeFieldDenormalizer($accessor, ConvertTypeFieldDenormalizer::TYPE_BOOL);
        $fieldDenormalizer->denormalizeField('value', $object, 1.0, $context);
    }

    public function testDenormalizeFieldWithIntWhichCantBeConvertedToBool(): void
    {
        $object = new \stdClass();

        /** @var AccessorInterface|MockObject $accessor */
        $accessor = $this->getMockByCalls(AccessorInterface::class, [
            Call::create('setValue')->with($object, 1),
        ]);

        /** @var DenormalizerContextInterface|MockObject $context */
        $context = $this->getMockByCalls(DenormalizerContextInterface::class);

        $fieldDenormalizer = new ConvertTypeFieldDenormalizer($accessor, ConvertTypeFieldDenormalizer::TYPE_BOOL);
        $fieldDenormalizer->denormalizeField('value', $object, 1, $context);
    }

    public function testDenormalizeFieldWithStringConvertedToTrue(): void
    {
        $object = new \stdClass();

        /** @var AccessorInterface|MockObject $accessor */
        $accessor = $this->getMockByCalls(AccessorInterface::class, [
            Call::create('setValue')->with($object, true),
        ]);

        /** @var DenormalizerContextInterface|MockObject $context */
        $context = $this->getMockByCalls(DenormalizerContextInterface::class);

        $fieldDenormalizer = new ConvertTypeFieldDenormalizer($accessor, ConvertTypeFieldDenormalizer::TYPE_BOOL);
        $fieldDenormalizer->denormalizeField('value', $object, 'true', $context);
    }

    public function testDenormalizeFieldWithStringConvertedToFalse(): void
    {
        $object = new \stdClass();

        /** @var AccessorInterface|MockObject $accessor */
        $accessor = $this->getMockByCalls(AccessorInterface::class, [
            Call::create('setValue')->with($object, false),
        ]);

        /** @var DenormalizerContextInterface|MockObject $context */
        $context = $this->getMockByCalls(DenormalizerContextInterface::class);

        $fieldDenormalizer = new ConvertTypeFieldDenormalizer($accessor, ConvertTypeFieldDenormalizer::TYPE_BOOL);
        $fieldDenormalizer->denormalizeField('value', $object, 'false', $context);
    }

    public function testDenormalizeFieldWithStringWhichCantBeConvertedToBool(): void
    {
        $object = new \stdClass();

        /** @var AccessorInterface|MockObject $accessor */
        $accessor = $this->getMockByCalls(AccessorInterface::class, [
            Call::create('setValue')->with($object, 'test'),
        ]);

        /** @var DenormalizerContextInterface|MockObject $context */
        $context = $this->getMockByCalls(DenormalizerContextInterface::class);

        $fieldDenormalizer = new ConvertTypeFieldDenormalizer($accessor, ConvertTypeFieldDenormalizer::TYPE_BOOL);
        $fieldDenormalizer->denormalizeField('value', $object, 'test', $context);
    }

    public function testDenormalizeFieldWithBoolWhichCantBeConvertedToFloat(): void
    {
        $object = new \stdClass();

        /** @var AccessorInterface|MockObject $accessor */
        $accessor = $this->getMockByCalls(AccessorInterface::class, [
            Call::create('setValue')->with($object, true),
        ]);

        /** @var DenormalizerContextInterface|MockObject $context */
        $context = $this->getMockByCalls(DenormalizerContextInterface::class);

        $fieldDenormalizer = new ConvertTypeFieldDenormalizer($accessor, ConvertTypeFieldDenormalizer::TYPE_FLOAT);
        $fieldDenormalizer->denormalizeField('value', $object, true, $context);
    }

    public function testDenormalizeFieldWithIntegerConvertedToFloat(): void
    {
        $object = new \stdClass();

        /** @var AccessorInterface|MockObject $accessor */
        $accessor = $this->getMockByCalls(AccessorInterface::class, [
            Call::create('setValue')->with($object, 1.0),
        ]);

        /** @var DenormalizerContextInterface|MockObject $context */
        $context = $this->getMockByCalls(DenormalizerContextInterface::class);

        $fieldDenormalizer = new ConvertTypeFieldDenormalizer($accessor, ConvertTypeFieldDenormalizer::TYPE_FLOAT);
        $fieldDenormalizer->denormalizeField('value', $object, 1, $context);
    }

    public function testDenormalizeFieldWithSpecialIntegerConvertedToFloat(): void
    {
        $object = new \stdClass();

        /** @var AccessorInterface|MockObject $accessor */
        $accessor = $this->getMockByCalls(AccessorInterface::class, [
            Call::create('setValue')->with($object, 1337.0),
            Call::create('setValue')->with($object, 1337.0),
            Call::create('setValue')->with($object, 1337.0),
            Call::create('setValue')->with($object, 1337.0),
        ]);

        /** @var DenormalizerContextInterface|MockObject $context */
        $context = $this->getMockByCalls(DenormalizerContextInterface::class);

        $fieldDenormalizer = new ConvertTypeFieldDenormalizer($accessor, ConvertTypeFieldDenormalizer::TYPE_FLOAT);

        $fieldDenormalizer->denormalizeField('value', $object, 0x539, $context);
        $fieldDenormalizer->denormalizeField('value', $object, 0b10100111001, $context);
        $fieldDenormalizer->denormalizeField('value', $object, 0o2471, $context);
        $fieldDenormalizer->denormalizeField('value', $object, 1337e0, $context);
    }

    public function testDenormalizeFieldWithStringWhichCantBeConvertedToFloat(): void
    {
        $object = new \stdClass();

        /** @var AccessorInterface|MockObject $accessor */
        $accessor = $this->getMockByCalls(AccessorInterface::class, [
            Call::create('setValue')->with($object, '5.5.5'),
        ]);

        /** @var DenormalizerContextInterface|MockObject $context */
        $context = $this->getMockByCalls(DenormalizerContextInterface::class);

        $fieldDenormalizer = new ConvertTypeFieldDenormalizer($accessor, ConvertTypeFieldDenormalizer::TYPE_FLOAT);
        $fieldDenormalizer->denormalizeField('value', $object, '5.5.5', $context);
    }

    public function testDenormalizeFieldWithStringConvertedToFloat(): void
    {
        $object = new \stdClass();

        /** @var AccessorInterface|MockObject $accessor */
        $accessor = $this->getMockByCalls(AccessorInterface::class, [
            Call::create('setValue')->with($object, 5.5),
        ]);

        /** @var DenormalizerContextInterface|MockObject $context */
        $context = $this->getMockByCalls(DenormalizerContextInterface::class);

        $fieldDenormalizer = new ConvertTypeFieldDenormalizer($accessor, ConvertTypeFieldDenormalizer::TYPE_FLOAT);
        $fieldDenormalizer->denormalizeField('value', $object, '5.500', $context);
    }

    public function testDenormalizeFieldWithBoolWhichCantBeConvertedToInteger(): void
    {
        $object = new \stdClass();

        /** @var AccessorInterface|MockObject $accessor */
        $accessor = $this->getMockByCalls(AccessorInterface::class, [
            Call::create('setValue')->with($object, true),
        ]);

        /** @var DenormalizerContextInterface|MockObject $context */
        $context = $this->getMockByCalls(DenormalizerContextInterface::class);

        $fieldDenormalizer = new ConvertTypeFieldDenormalizer($accessor, ConvertTypeFieldDenormalizer::TYPE_INT);
        $fieldDenormalizer->denormalizeField('value', $object, true, $context);
    }

    public function testDenormalizeFieldWithFloatConvertedToInteger(): void
    {
        $object = new \stdClass();

        /** @var AccessorInterface|MockObject $accessor */
        $accessor = $this->getMockByCalls(AccessorInterface::class, [
            Call::create('setValue')->with($object, 5),
        ]);

        /** @var DenormalizerContextInterface|MockObject $context */
        $context = $this->getMockByCalls(DenormalizerContextInterface::class);

        $fieldDenormalizer = new ConvertTypeFieldDenormalizer($accessor, ConvertTypeFieldDenormalizer::TYPE_INT);
        $fieldDenormalizer->denormalizeField('value', $object, 5.0, $context);
    }

    public function testDenormalizeFieldWithFloatWhichCantBeConvertedToInteger(): void
    {
        $object = new \stdClass();

        /** @var AccessorInterface|MockObject $accessor */
        $accessor = $this->getMockByCalls(AccessorInterface::class, [
            Call::create('setValue')->with($object, 5.1),
        ]);

        /** @var DenormalizerContextInterface|MockObject $context */
        $context = $this->getMockByCalls(DenormalizerContextInterface::class);

        $fieldDenormalizer = new ConvertTypeFieldDenormalizer($accessor, ConvertTypeFieldDenormalizer::TYPE_INT);
        $fieldDenormalizer->denormalizeField('value', $object, 5.1, $context);
    }

    public function testDenormalizeFieldWithStringWhichCantBeConvertedToInteger(): void
    {
        $object = new \stdClass();

        /** @var AccessorInterface|MockObject $accessor */
        $accessor = $this->getMockByCalls(AccessorInterface::class, [
            Call::create('setValue')->with($object, 'test'),
        ]);

        /** @var DenormalizerContextInterface|MockObject $context */
        $context = $this->getMockByCalls(DenormalizerContextInterface::class);

        $fieldDenormalizer = new ConvertTypeFieldDenormalizer($accessor, ConvertTypeFieldDenormalizer::TYPE_INT);
        $fieldDenormalizer->denormalizeField('value', $object, 'test', $context);
    }

    public function testDenormalizeFieldWithStringConvertedToInteger(): void
    {
        $object = new \stdClass();

        /** @var AccessorInterface|MockObject $accessor */
        $accessor = $this->getMockByCalls(AccessorInterface::class, [
            Call::create('setValue')->with($object, 5),
        ]);

        /** @var DenormalizerContextInterface|MockObject $context */
        $context = $this->getMockByCalls(DenormalizerContextInterface::class);

        $fieldDenormalizer = new ConvertTypeFieldDenormalizer($accessor, ConvertTypeFieldDenormalizer::TYPE_INT);
        $fieldDenormalizer->denormalizeField('value', $object, '5', $context);
    }

    public function testDenormalizeFieldWithBoolWhichCantBeConvertedToString(): void
    {
        $object = new \stdClass();

        /** @var AccessorInterface|MockObject $accessor */
        $accessor = $this->getMockByCalls(AccessorInterface::class, [
            Call::create('setValue')->with($object, true),
        ]);

        /** @var DenormalizerContextInterface|MockObject $context */
        $context = $this->getMockByCalls(DenormalizerContextInterface::class);

        $fieldDenormalizer = new ConvertTypeFieldDenormalizer($accessor, ConvertTypeFieldDenormalizer::TYPE_STRING);
        $fieldDenormalizer->denormalizeField('value', $object, true, $context);
    }

    public function testDenormalizeFieldWithFloatConvertedToString(): void
    {
        $object = new \stdClass();

        /** @var AccessorInterface|MockObject $accessor */
        $accessor = $this->getMockByCalls(AccessorInterface::class, [
            Call::create('setValue')->with($object, '5.5'),
        ]);

        /** @var DenormalizerContextInterface|MockObject $context */
        $context = $this->getMockByCalls(DenormalizerContextInterface::class);

        $fieldDenormalizer = new ConvertTypeFieldDenormalizer($accessor, ConvertTypeFieldDenormalizer::TYPE_STRING);
        $fieldDenormalizer->denormalizeField('value', $object, 5.5, $context);
    }

    public function testDenormalizeFieldWithIntegerConvertedToString(): void
    {
        $object = new \stdClass();

        /** @var AccessorInterface|MockObject $accessor */
        $accessor = $this->getMockByCalls(AccessorInterface::class, [
            Call::create('setValue')->with($object, '5'),
        ]);

        /** @var DenormalizerContextInterface|MockObject $context */
        $context = $this->getMockByCalls(DenormalizerContextInterface::class);

        $fieldDenormalizer = new ConvertTypeFieldDenormalizer($accessor, ConvertTypeFieldDenormalizer::TYPE_STRING);
        $fieldDenormalizer->denormalizeField('value', $object, 5, $context);
    }

    public function testDenormalizeFieldWithSpecialIntegerConvertedToString(): void
    {
        $object = new \stdClass();

        /** @var AccessorInterface|MockObject $accessor */
        $accessor = $this->getMockByCalls(AccessorInterface::class, [
            Call::create('setValue')->with($object, '1337'),
            Call::create('setValue')->with($object, '1337'),
            Call::create('setValue')->with($object, '1337'),
            Call::create('setValue')->with($object, '1337'),
        ]);

        /** @var DenormalizerContextInterface|MockObject $context */
        $context = $this->getMockByCalls(DenormalizerContextInterface::class);

        $fieldDenormalizer = new ConvertTypeFieldDenormalizer($accessor, ConvertTypeFieldDenormalizer::TYPE_STRING);

        $fieldDenormalizer->denormalizeField('value', $object, 0x539, $context);
        $fieldDenormalizer->denormalizeField('value', $object, 0b10100111001, $context);
        $fieldDenormalizer->denormalizeField('value', $object, 0o2471, $context);
        $fieldDenormalizer->denormalizeField('value', $object, 1337e0, $context);
    }

    public function testDenormalizeFieldWithEmptyToNullDisabled(): void
    {
        $object = new \stdClass();

        /** @var AccessorInterface|MockObject $accessor */
        $accessor = $this->getMockByCalls(AccessorInterface::class, [
            Call::create('setValue')->with($object, ''),
        ]);

        /** @var DenormalizerContextInterface|MockObject $context */
        $context = $this->getMockByCalls(DenormalizerContextInterface::class);

        $fieldDenormalizer = new ConvertTypeFieldDenormalizer($accessor, ConvertTypeFieldDenormalizer::TYPE_FLOAT);
        $fieldDenormalizer->denormalizeField('value', $object, '', $context);
    }

    public function testDenormalizeFieldWithObjectToString(): void
    {
        $object = new \stdClass();
        $valueObject = new \stdClass();

        /** @var AccessorInterface|MockObject $accessor */
        $accessor = $this->getMockByCalls(AccessorInterface::class, [
            Call::create('setValue')->with($object, $valueObject),
        ]);

        /** @var DenormalizerContextInterface|MockObject $context */
        $context = $this->getMockByCalls(DenormalizerContextInterface::class);

        $fieldDenormalizer = new ConvertTypeFieldDenormalizer($accessor, ConvertTypeFieldDenormalizer::TYPE_STRING);
        $fieldDenormalizer->denormalizeField('value', $object, $valueObject, $context);
    }

    public function testDenormalizeFieldWithEmptyToNullEnabled(): void
    {
        $object = new \stdClass();

        /** @var AccessorInterface|MockObject $accessor */
        $accessor = $this->getMockByCalls(AccessorInterface::class, [
            Call::create('setValue')->with($object, null),
        ]);

        /** @var DenormalizerContextInterface|MockObject $context */
        $context = $this->getMockByCalls(DenormalizerContextInterface::class);

        $fieldDenormalizer = new ConvertTypeFieldDenormalizer($accessor, ConvertTypeFieldDenormalizer::TYPE_FLOAT, true);
        $fieldDenormalizer->denormalizeField('value', $object, '', $context);
    }
}
