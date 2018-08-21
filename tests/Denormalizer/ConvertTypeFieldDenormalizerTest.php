<?php

declare(strict_types=1);

namespace Chubbyphp\Tests\Deserialization\Denormalizer;

use Chubbyphp\Deserialization\Accessor\AccessorInterface;
use Chubbyphp\Deserialization\Denormalizer\DenormalizerContextInterface;
use Chubbyphp\Deserialization\Denormalizer\ConvertTypeFieldDenormalizer;
use Chubbyphp\Deserialization\DeserializerLogicException;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Chubbyphp\Deserialization\Denormalizer\ConvertTypeFieldDenormalizer
 */
class ConvertTypeFieldDenormalizerTest extends TestCase
{
    public function testDenormalizeFieldWithInvalidType()
    {
        $this->expectException(DeserializerLogicException::class);
        $this->expectExceptionMessage('Convert type "type" is not supported');

        new ConvertTypeFieldDenormalizer($this->getAccessor(), 'type');
    }

    public function testDenormalizeFieldWithNull()
    {
        $object = $this->getObject();

        $fieldDenormalizer = new ConvertTypeFieldDenormalizer($this->getAccessor(), ConvertTypeFieldDenormalizer::TYPE_INT);
        $fieldDenormalizer->denormalizeField('value', $object, null, $this->getDenormalizerContext());

        self::assertNull($object->getValue());
    }

    public function testDenormalizeFieldWithArray()
    {
        $object = $this->getObject();

        $fieldDenormalizer = new ConvertTypeFieldDenormalizer($this->getAccessor(), ConvertTypeFieldDenormalizer::TYPE_INT);
        $fieldDenormalizer->denormalizeField('value', $object, [], $this->getDenormalizerContext());

        self::assertSame([], $object->getValue());
    }

    public function testDenormalizeFieldWithFloatWhichCantBeConvertedToBool()
    {
        $object = $this->getObject();

        $fieldDenormalizer = new ConvertTypeFieldDenormalizer($this->getAccessor(), ConvertTypeFieldDenormalizer::TYPE_BOOL);
        $fieldDenormalizer->denormalizeField('value', $object, 1.0, $this->getDenormalizerContext());

        self::assertSame(1.0, $object->getValue());
    }

    public function testDenormalizeFieldWithIntWhichCantBeConvertedToBool()
    {
        $object = $this->getObject();

        $fieldDenormalizer = new ConvertTypeFieldDenormalizer($this->getAccessor(), ConvertTypeFieldDenormalizer::TYPE_BOOL);
        $fieldDenormalizer->denormalizeField('value', $object, 1, $this->getDenormalizerContext());

        self::assertSame(1, $object->getValue());
    }

    public function testDenormalizeFieldWithStringConvertedToTrue()
    {
        $object = $this->getObject();

        $fieldDenormalizer = new ConvertTypeFieldDenormalizer($this->getAccessor(), ConvertTypeFieldDenormalizer::TYPE_BOOL);
        $fieldDenormalizer->denormalizeField('value', $object, 'true', $this->getDenormalizerContext());

        self::assertTrue($object->getValue());
    }

    public function testDenormalizeFieldWithStringConvertedToFalse()
    {
        $object = $this->getObject();

        $fieldDenormalizer = new ConvertTypeFieldDenormalizer($this->getAccessor(), ConvertTypeFieldDenormalizer::TYPE_BOOL);
        $fieldDenormalizer->denormalizeField('value', $object, 'false', $this->getDenormalizerContext());

        self::assertFalse($object->getValue());
    }

    public function testDenormalizeFieldWithStringWhichCantBeConvertedToBool()
    {
        $object = $this->getObject();

        $fieldDenormalizer = new ConvertTypeFieldDenormalizer($this->getAccessor(), ConvertTypeFieldDenormalizer::TYPE_BOOL);
        $fieldDenormalizer->denormalizeField('value', $object, 'test', $this->getDenormalizerContext());

        self::assertSame('test', $object->getValue());
    }

    public function testDenormalizeFieldWithBoolWhichCantBeConvertedToFloat()
    {
        $object = $this->getObject();

        $fieldDenormalizer = new ConvertTypeFieldDenormalizer($this->getAccessor(), ConvertTypeFieldDenormalizer::TYPE_FLOAT);
        $fieldDenormalizer->denormalizeField('value', $object, true, $this->getDenormalizerContext());

        self::assertTrue($object->getValue());
    }

    public function testDenormalizeFieldWithIntegerConvertedToFloat()
    {
        $object = $this->getObject();

        $fieldDenormalizer = new ConvertTypeFieldDenormalizer($this->getAccessor(), ConvertTypeFieldDenormalizer::TYPE_FLOAT);
        $fieldDenormalizer->denormalizeField('value', $object, 1, $this->getDenormalizerContext());

        self::assertSame(1.0, $object->getValue());
    }

    public function testDenormalizeFieldWithSpecialIntegerConvertedToFloat()
    {
        $object = $this->getObject();

        $fieldDenormalizer = new ConvertTypeFieldDenormalizer($this->getAccessor(), ConvertTypeFieldDenormalizer::TYPE_FLOAT);

        $fieldDenormalizer->denormalizeField('value', $object, 0x539, $this->getDenormalizerContext());

        self::assertSame(1337.0, $object->getValue());

        $fieldDenormalizer->denormalizeField('value', $object, 0b10100111001, $this->getDenormalizerContext());

        self::assertSame(1337.0, $object->getValue());

        $fieldDenormalizer->denormalizeField('value', $object, 02471, $this->getDenormalizerContext());

        self::assertSame(1337.0, $object->getValue());

        $fieldDenormalizer->denormalizeField('value', $object, 1337e0, $this->getDenormalizerContext());

        self::assertSame(1337.0, $object->getValue());
    }

    public function testDenormalizeFieldWithStringWhichCantBeConvertedToFloat()
    {
        $object = $this->getObject();

        $fieldDenormalizer = new ConvertTypeFieldDenormalizer($this->getAccessor(), ConvertTypeFieldDenormalizer::TYPE_FLOAT);
        $fieldDenormalizer->denormalizeField('value', $object, '5.5.5', $this->getDenormalizerContext());

        self::assertSame('5.5.5', $object->getValue());
    }

    public function testDenormalizeFieldWithStringConvertedToFloat()
    {
        $object = $this->getObject();

        $fieldDenormalizer = new ConvertTypeFieldDenormalizer($this->getAccessor(), ConvertTypeFieldDenormalizer::TYPE_FLOAT);
        $fieldDenormalizer->denormalizeField('value', $object, '5.500', $this->getDenormalizerContext());

        self::assertSame(5.5, $object->getValue());
    }

    public function testDenormalizeFieldWithBoolWhichCantBeConvertedToInteger()
    {
        $object = $this->getObject();

        $fieldDenormalizer = new ConvertTypeFieldDenormalizer($this->getAccessor(), ConvertTypeFieldDenormalizer::TYPE_INT);
        $fieldDenormalizer->denormalizeField('value', $object, true, $this->getDenormalizerContext());

        self::assertTrue($object->getValue());
    }

    public function testDenormalizeFieldWithFloatConvertedToInteger()
    {
        $object = $this->getObject();

        $fieldDenormalizer = new ConvertTypeFieldDenormalizer($this->getAccessor(), ConvertTypeFieldDenormalizer::TYPE_INT);
        $fieldDenormalizer->denormalizeField('value', $object, 5.0, $this->getDenormalizerContext());

        self::assertSame(5, $object->getValue());
    }

    public function testDenormalizeFieldWithFloatWhichCantBeConvertedToInteger()
    {
        $object = $this->getObject();

        $fieldDenormalizer = new ConvertTypeFieldDenormalizer($this->getAccessor(), ConvertTypeFieldDenormalizer::TYPE_INT);
        $fieldDenormalizer->denormalizeField('value', $object, 5.1, $this->getDenormalizerContext());

        self::assertSame(5.1, $object->getValue());
    }

    public function testDenormalizeFieldWithStringWhichCantBeConvertedToInteger()
    {
        $object = $this->getObject();

        $fieldDenormalizer = new ConvertTypeFieldDenormalizer($this->getAccessor(), ConvertTypeFieldDenormalizer::TYPE_INT);
        $fieldDenormalizer->denormalizeField('value', $object, 'test', $this->getDenormalizerContext());

        self::assertSame('test', $object->getValue());
    }

    public function testDenormalizeFieldWithStringConvertedToInteger()
    {
        $object = $this->getObject();

        $fieldDenormalizer = new ConvertTypeFieldDenormalizer($this->getAccessor(), ConvertTypeFieldDenormalizer::TYPE_INT);
        $fieldDenormalizer->denormalizeField('value', $object, '5', $this->getDenormalizerContext());

        self::assertSame(5, $object->getValue());
    }

    public function testDenormalizeFieldWithBoolWhichCantBeConvertedToString()
    {
        $object = $this->getObject();

        $fieldDenormalizer = new ConvertTypeFieldDenormalizer($this->getAccessor(), ConvertTypeFieldDenormalizer::TYPE_STRING);
        $fieldDenormalizer->denormalizeField('value', $object, true, $this->getDenormalizerContext());

        self::assertTrue($object->getValue());
    }

    public function testDenormalizeFieldWithFloatConvertedToString()
    {
        $object = $this->getObject();

        $fieldDenormalizer = new ConvertTypeFieldDenormalizer($this->getAccessor(), ConvertTypeFieldDenormalizer::TYPE_STRING);
        $fieldDenormalizer->denormalizeField('value', $object, 5.5, $this->getDenormalizerContext());

        self::assertSame('5.5', $object->getValue());
    }

    public function testDenormalizeFieldWithIntegerConvertedToString()
    {
        $object = $this->getObject();

        $fieldDenormalizer = new ConvertTypeFieldDenormalizer($this->getAccessor(), ConvertTypeFieldDenormalizer::TYPE_STRING);
        $fieldDenormalizer->denormalizeField('value', $object, 5, $this->getDenormalizerContext());

        self::assertSame('5', $object->getValue());
    }

    public function testDenormalizeFieldWithSpecialIntegerConvertedToString()
    {
        $object = $this->getObject();

        $fieldDenormalizer = new ConvertTypeFieldDenormalizer($this->getAccessor(), ConvertTypeFieldDenormalizer::TYPE_STRING);

        $fieldDenormalizer->denormalizeField('value', $object, 0x539, $this->getDenormalizerContext());

        self::assertSame('1337', $object->getValue());

        $fieldDenormalizer->denormalizeField('value', $object, 0b10100111001, $this->getDenormalizerContext());

        self::assertSame('1337', $object->getValue());

        $fieldDenormalizer->denormalizeField('value', $object, 02471, $this->getDenormalizerContext());

        self::assertSame('1337', $object->getValue());

        $fieldDenormalizer->denormalizeField('value', $object, 1337e0, $this->getDenormalizerContext());

        self::assertSame('1337', $object->getValue());
    }

    private function getObject()
    {
        return new class() {
            /**
             * @var int
             */
            private $value;

            /**
             * @param int $value
             *
             * @return self
             */
            public function setValue($value): self
            {
                $this->value = $value;

                return $this;
            }

            /**
             * @return int
             */
            public function getValue()
            {
                return $this->value;
            }
        };
    }

    /**
     * @return AccessorInterface
     */
    private function getAccessor(): AccessorInterface
    {
        /** @var AccessorInterface|\PHPUnit_Framework_MockObject_MockObject $accessor */
        $accessor = $this->getMockBuilder(AccessorInterface::class)->getMockForAbstractClass();

        $accessor->expects(self::any())->method('setValue')->willReturnCallback(function ($object, $value) {
            $object->setValue($value);
        });

        $accessor->expects(self::any())->method('getValue')->willReturnCallback(function ($object) {
            return $object->getValue();
        });

        return $accessor;
    }

    /**
     * @return DenormalizerContextInterface
     */
    private function getDenormalizerContext(): DenormalizerContextInterface
    {
        /** @var DenormalizerContextInterface|\PHPUnit_Framework_MockObject_MockObject $context */
        $context = $this->getMockBuilder(DenormalizerContextInterface::class)->getMockForAbstractClass();

        return $context;
    }
}
