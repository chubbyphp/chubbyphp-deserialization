<?php

declare(strict_types=1);

namespace Chubbyphp\Tests\Deserialization\Denormalizer;

use Chubbyphp\Deserialization\Denormalizer\DateFieldDenormalizer;
use Chubbyphp\Deserialization\Denormalizer\DenormalizerContextInterface;
use Chubbyphp\Deserialization\Denormalizer\FieldDenormalizerInterface;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Chubbyphp\Deserialization\Denormalizer\DateFieldDenormalizer
 */
class DateFieldDenormalizerTest extends TestCase
{
    public function testDenormalizeField()
    {
        $object = $this->getObject();
        $object->setDate(new \DateTime('2016-01-01'));

        $fieldDenormalizer = new DateFieldDenormalizer($this->getFieldDenormalizer());
        $fieldDenormalizer->denormalizeField('date', $object, '2017-01-01', $this->getDenormalizerContext());

        self::assertSame('2017-01-01', $object->getDate()->format('Y-m-d'));
    }

    public function testDenormalizeInvalidField()
    {
        $object = $this->getObject();
        $object->setDate(new \DateTime('2016-01-01'));

        $fieldDenormalizer = new DateFieldDenormalizer($this->getFieldDenormalizer());
        $fieldDenormalizer->denormalizeField('date', $object, '2017-13-01', $this->getDenormalizerContext());

        self::assertSame('2017-13-01', $object->getDate());

        self::assertNull(error_get_last());
    }

    public function testDenormalizeEmptyField()
    {
        $object = $this->getObject();
        $object->setDate(new \DateTime('2016-01-01'));

        $fieldDenormalizer = new DateFieldDenormalizer($this->getFieldDenormalizer());
        $fieldDenormalizer->denormalizeField('date', $object, '', $this->getDenormalizerContext());

        self::assertSame('', $object->getDate());
    }

    public function testDenormalizeWhitespaceOnlyField()
    {
        $object = $this->getObject();
        $object->setDate(new \DateTime('2016-01-01'));

        $fieldDenormalizer = new DateFieldDenormalizer($this->getFieldDenormalizer());
        $fieldDenormalizer->denormalizeField('date', $object, '   ', $this->getDenormalizerContext());

        self::assertSame('   ', $object->getDate());
    }

    public function testDenormalizeNullField()
    {
        $object = $this->getObject();
        $object->setDate(new \DateTime('2016-01-01'));

        $fieldDenormalizer = new DateFieldDenormalizer($this->getFieldDenormalizer());
        $fieldDenormalizer->denormalizeField('date', $object, null, $this->getDenormalizerContext());

        self::assertSame(null, $object->getDate());
    }

    public function testDenormalizeNullStringField()
    {
        $object = $this->getObject();
        $object->setDate(new \DateTime('2016-01-01'));

        $fieldDenormalizer = new DateFieldDenormalizer($this->getFieldDenormalizer());
        $fieldDenormalizer->denormalizeField('date', $object, 'null', $this->getDenormalizerContext());

        self::assertSame('null', $object->getDate());

        self::assertNull(error_get_last());
    }

    public function testDenormalizeZeroField()
    {
        $object = $this->getObject();
        $object->setDate(new \DateTime('2016-01-01'));

        $fieldDenormalizer = new DateFieldDenormalizer($this->getFieldDenormalizer());
        $fieldDenormalizer->denormalizeField('date', $object, 0, $this->getDenormalizerContext());

        self::assertSame(0, $object->getDate());

        self::assertNull(error_get_last());
    }

    public function testDenormalizeZeroStringField()
    {
        $object = $this->getObject();
        $object->setDate(new \DateTime('2016-01-01'));

        $fieldDenormalizer = new DateFieldDenormalizer($this->getFieldDenormalizer());
        $fieldDenormalizer->denormalizeField('date', $object, '0', $this->getDenormalizerContext());

        self::assertSame('0', $object->getDate());

        self::assertNull(error_get_last());
    }

    public function testDenormalizeArrayField()
    {
        $object = $this->getObject();
        $object->setDate(new \DateTime('2016-01-01'));

        $fieldDenormalizer = new DateFieldDenormalizer($this->getFieldDenormalizer());
        $fieldDenormalizer->denormalizeField('date', $object, [], $this->getDenormalizerContext());

        self::assertSame([], $object->getDate());
    }

    public function testDenormalizeObjectField()
    {
        $object = $this->getObject();
        $object->setDate(new \DateTime('2016-01-01'));

        $date = new \stdClass();

        $fieldDenormalizer = new DateFieldDenormalizer($this->getFieldDenormalizer());
        $fieldDenormalizer->denormalizeField('date', $object, $date, $this->getDenormalizerContext());

        self::assertSame($date, $object->getDate());
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
