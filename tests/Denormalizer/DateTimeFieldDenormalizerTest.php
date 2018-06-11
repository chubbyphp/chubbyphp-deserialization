<?php

declare(strict_types=1);

namespace Chubbyphp\Tests\Deserialization\Denormalizer;

use Chubbyphp\Deserialization\Accessor\AccessorInterface;
use Chubbyphp\Deserialization\Denormalizer\DateTimeFieldDenormalizer;
use Chubbyphp\Deserialization\Denormalizer\DenormalizerContextInterface;
use Chubbyphp\Deserialization\Denormalizer\FieldDenormalizerInterface;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Chubbyphp\Deserialization\Denormalizer\DateTimeFieldDenormalizer
 */
class DateTimeFieldDenormalizerTest extends TestCase
{
    public function testDenormalizeFieldWithInvalidConstructArgument()
    {
        self::expectException(\TypeError::class);
        self::expectExceptionMessage('Chubbyphp\Deserialization\Denormalizer\DateTimeFieldDenormalizer::__construct() expects parameter 1 to be Chubbyphp\Deserialization\Accessor\AccessorInterface|Chubbyphp\Deserialization\Denormalizer\FieldDenormalizerInterface, DateTime given');

        new DateTimeFieldDenormalizer(new \DateTime());
    }

    public function testDenormalizeFieldWithFieldDenormalizer()
    {
        $object = $this->getObject();
        $object->setDate(new \DateTime('2016-01-01'));

        $fieldDenormalizer = new DateTimeFieldDenormalizer($this->getFieldDenormalizer());
        $fieldDenormalizer->denormalizeField('date', $object, '2017-01-01', $this->getDenormalizerContext());

        self::assertSame('2017-01-01', $object->getDate()->format('Y-m-d'));

        $error = error_get_last();

        error_clear_last();

        self::assertNotNull($error);

        self::assertSame(E_USER_DEPRECATED, $error['type']);
        self::assertSame('Use "Chubbyphp\\Deserialization\\Accessor\\AccessorInterface" instead of "Chubbyphp\\Deserialization\\Denormalizer\\FieldDenormalizerInterface" as __construct argument', $error['message']);
    }

    public function testDenormalizeField()
    {
        $object = $this->getObject();
        $object->setDate(new \DateTime('2016-01-01'));

        $fieldDenormalizer = new DateTimeFieldDenormalizer($this->getAccessor());
        $fieldDenormalizer->denormalizeField('date', $object, '2017-01-01', $this->getDenormalizerContext());

        self::assertSame('2017-01-01', $object->getDate()->format('Y-m-d'));

        self::assertNull(error_get_last());
    }

    public function testDenormalizeInvalidMonthField()
    {
        $object = $this->getObject();
        $object->setDate(new \DateTime('2016-01-01'));

        $fieldDenormalizer = new DateTimeFieldDenormalizer($this->getAccessor());
        $fieldDenormalizer->denormalizeField('date', $object, '2017-13-01', $this->getDenormalizerContext());

        self::assertSame('2017-13-01', $object->getDate());

        self::assertNull(error_get_last());
    }

    public function testDenormalizeInvalidDayField()
    {
        $object = $this->getObject();
        $object->setDate(new \DateTime('2016-01-01'));

        $fieldDenormalizer = new DateTimeFieldDenormalizer($this->getAccessor());
        $fieldDenormalizer->denormalizeField('date', $object, '2017-02-31', $this->getDenormalizerContext());

        self::assertSame('2017-02-31', $object->getDate());

        self::assertNull(error_get_last());
    }

    public function testDenormalizeInvalidWithAllZeroField()
    {
        $object = $this->getObject();
        $object->setDate(new \DateTime('2016-01-01'));

        $fieldDenormalizer = new DateTimeFieldDenormalizer($this->getAccessor());
        $fieldDenormalizer->denormalizeField('date', $object, '0000-00-00', $this->getDenormalizerContext());

        self::assertSame('0000-00-00', $object->getDate());

        self::assertNull(error_get_last());
    }

    public function testDenormalizeEmptyField()
    {
        $object = $this->getObject();
        $object->setDate(new \DateTime('2016-01-01'));

        $fieldDenormalizer = new DateTimeFieldDenormalizer($this->getAccessor());
        $fieldDenormalizer->denormalizeField('date', $object, '', $this->getDenormalizerContext());

        self::assertSame('', $object->getDate());

        self::assertNull(error_get_last());
    }

    public function testDenormalizeWhitespaceOnlyField()
    {
        $object = $this->getObject();
        $object->setDate(new \DateTime('2016-01-01'));

        $fieldDenormalizer = new DateTimeFieldDenormalizer($this->getAccessor());
        $fieldDenormalizer->denormalizeField('date', $object, '   ', $this->getDenormalizerContext());

        self::assertSame('   ', $object->getDate());

        self::assertNull(error_get_last());
    }

    public function testDenormalizeNullField()
    {
        $object = $this->getObject();
        $object->setDate(new \DateTime('2016-01-01'));

        $fieldDenormalizer = new DateTimeFieldDenormalizer($this->getAccessor());
        $fieldDenormalizer->denormalizeField('date', $object, null, $this->getDenormalizerContext());

        self::assertSame(null, $object->getDate());

        self::assertNull(error_get_last());
    }

    public function testDenormalizeNullStringField()
    {
        $object = $this->getObject();
        $object->setDate(new \DateTime('2016-01-01'));

        $fieldDenormalizer = new DateTimeFieldDenormalizer($this->getAccessor());
        $fieldDenormalizer->denormalizeField('date', $object, 'null', $this->getDenormalizerContext());

        self::assertSame('null', $object->getDate());

        self::assertNull(error_get_last());
    }

    public function testDenormalizeZeroField()
    {
        $object = $this->getObject();
        $object->setDate(new \DateTime('2016-01-01'));

        $fieldDenormalizer = new DateTimeFieldDenormalizer($this->getAccessor());
        $fieldDenormalizer->denormalizeField('date', $object, 0, $this->getDenormalizerContext());

        self::assertSame(0, $object->getDate());

        self::assertNull(error_get_last());
    }

    public function testDenormalizeZeroStringField()
    {
        $object = $this->getObject();
        $object->setDate(new \DateTime('2016-01-01'));

        $fieldDenormalizer = new DateTimeFieldDenormalizer($this->getAccessor());
        $fieldDenormalizer->denormalizeField('date', $object, '0', $this->getDenormalizerContext());

        self::assertSame('0', $object->getDate());

        self::assertNull(error_get_last());
    }

    public function testDenormalizeArrayField()
    {
        $object = $this->getObject();
        $object->setDate(new \DateTime('2016-01-01'));

        $fieldDenormalizer = new DateTimeFieldDenormalizer($this->getAccessor());
        $fieldDenormalizer->denormalizeField('date', $object, [], $this->getDenormalizerContext());

        self::assertSame([], $object->getDate());

        self::assertNull(error_get_last());
    }

    public function testDenormalizeObjectField()
    {
        $object = $this->getObject();
        $object->setDate(new \DateTime('2016-01-01'));

        $date = new \stdClass();

        $fieldDenormalizer = new DateTimeFieldDenormalizer($this->getAccessor());
        $fieldDenormalizer->denormalizeField('date', $object, $date, $this->getDenormalizerContext());

        self::assertSame($date, $object->getDate());

        self::assertNull(error_get_last());
    }

    private function getObject()
    {
        return new class() {
            /**
             * @var \DateTime|string|null
             */
            private $date;

            /**
             * @return \DateTime|string|null
             */
            public function getDate()
            {
                return $this->date;
            }

            /**
             * @param \DateTime|string|null $date
             *
             * @return self
             */
            public function setDate($date): self
            {
                $this->date = $date;

                return $this;
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

        $accessor->expects(self::any())->method('setValue')->willReturnCallback(
            function ($object, $value) {
                $object->setDate($value);
            }
        );

        return $accessor;
    }

    /**
     * @return FieldDenormalizerInterface
     */
    private function getFieldDenormalizer(): FieldDenormalizerInterface
    {
        /** @var FieldDenormalizerInterface|\PHPUnit_Framework_MockObject_MockObject $fieldDenormalizer */
        $fieldDenormalizer = $this->getMockBuilder(FieldDenormalizerInterface::class)->getMockForAbstractClass();

        $fieldDenormalizer->expects(self::any())->method('denormalizeField')->willReturnCallback(
            function (string $path, $object, $value) {
                $object->setDate($value);
            }
        );

        return $fieldDenormalizer;
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
