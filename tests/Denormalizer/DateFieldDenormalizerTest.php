<?php

declare(strict_types=1);

namespace Chubbyphp\Tests\Deserialization\Denormalizer;

use Chubbyphp\Deserialization\Denormalizer\DateFieldDenormalizer;
use Chubbyphp\Deserialization\Denormalizer\DenormalizerContextInterface;
use Chubbyphp\Deserialization\Denormalizer\FieldDenormalizerInterface;
use Chubbyphp\Mock\Argument\ArgumentCallback;
use Chubbyphp\Mock\Call;
use Chubbyphp\Mock\MockByCallsTrait;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Chubbyphp\Deserialization\Denormalizer\DateFieldDenormalizer
 */
class DateFieldDenormalizerTest extends TestCase
{
    use MockByCallsTrait;

    public function testDenormalizeField()
    {
        $object = new \stdClass();

        /** @var DenormalizerContextInterface|MockObject $context */
        $context = $this->getMockByCalls(DenormalizerContextInterface::class);

        /** @var FieldDenormalizerInterface|MockObject $fieldDenormalizer */
        $fieldDenormalizer = $this->getMockByCalls(FieldDenormalizerInterface::class, [
            Call::create('denormalizeField')
                ->with(
                    'date',
                    $object,
                    new ArgumentCallback(
                        function ($value) {
                            self::assertInstanceOf(\DateTime::class, $value);
                            self::assertSame('2017-01-01', $value->format('Y-m-d'));
                        }
                    ),
                    $context,
                    null
                ),
        ]);

        $dateFieldDenormalizer = new DateFieldDenormalizer($fieldDenormalizer);
        $dateFieldDenormalizer->denormalizeField('date', $object, '2017-01-01', $context);
    }

    public function testDenormalizeInvalidMonthField()
    {
        $object = new \stdClass();

        /** @var DenormalizerContextInterface|MockObject $context */
        $context = $this->getMockByCalls(DenormalizerContextInterface::class);

        /** @var FieldDenormalizerInterface|MockObject $fieldDenormalizer */
        $fieldDenormalizer = $this->getMockByCalls(FieldDenormalizerInterface::class, [
            Call::create('denormalizeField')->with('date', $object, '2017-13-01', $context, null),
        ]);

        $dateFieldDenormalizer = new DateFieldDenormalizer($fieldDenormalizer);
        $dateFieldDenormalizer->denormalizeField('date', $object, '2017-13-01', $context);
    }

    public function testDenormalizeInvalidDayField()
    {
        $object = new \stdClass();

        /** @var DenormalizerContextInterface|MockObject $context */
        $context = $this->getMockByCalls(DenormalizerContextInterface::class);

        /** @var FieldDenormalizerInterface|MockObject $fieldDenormalizer */
        $fieldDenormalizer = $this->getMockByCalls(FieldDenormalizerInterface::class, [
            Call::create('denormalizeField')->with('date', $object, '2017-02-31', $context, null),
        ]);

        $dateFieldDenormalizer = new DateFieldDenormalizer($fieldDenormalizer);
        $dateFieldDenormalizer->denormalizeField('date', $object, '2017-02-31', $context);
    }

    public function testDenormalizeInvalidWithAllZeroField()
    {
        $object = new \stdClass();

        /** @var DenormalizerContextInterface|MockObject $context */
        $context = $this->getMockByCalls(DenormalizerContextInterface::class);

        /** @var FieldDenormalizerInterface|MockObject $fieldDenormalizer */
        $fieldDenormalizer = $this->getMockByCalls(FieldDenormalizerInterface::class, [
            Call::create('denormalizeField')->with('date', $object, '0000-00-00', $context, null),
        ]);

        $dateFieldDenormalizer = new DateFieldDenormalizer($fieldDenormalizer);
        $dateFieldDenormalizer->denormalizeField('date', $object, '0000-00-00', $context);
    }

    public function testDenormalizeEmptyField()
    {
        $object = new \stdClass();

        /** @var DenormalizerContextInterface|MockObject $context */
        $context = $this->getMockByCalls(DenormalizerContextInterface::class);

        /** @var FieldDenormalizerInterface|MockObject $fieldDenormalizer */
        $fieldDenormalizer = $this->getMockByCalls(FieldDenormalizerInterface::class, [
            Call::create('denormalizeField')->with('date', $object, '', $context, null),
        ]);

        $dateFieldDenormalizer = new DateFieldDenormalizer($fieldDenormalizer);
        $dateFieldDenormalizer->denormalizeField('date', $object, '', $context);
    }

    public function testDenormalizeWhitespaceOnlyField()
    {
        $object = new \stdClass();

        /** @var DenormalizerContextInterface|MockObject $context */
        $context = $this->getMockByCalls(DenormalizerContextInterface::class);

        /** @var FieldDenormalizerInterface|MockObject $fieldDenormalizer */
        $fieldDenormalizer = $this->getMockByCalls(FieldDenormalizerInterface::class, [
            Call::create('denormalizeField')->with('date', $object, '    ', $context, null),
        ]);

        $dateFieldDenormalizer = new DateFieldDenormalizer($fieldDenormalizer);
        $dateFieldDenormalizer->denormalizeField('date', $object, '    ', $context);
    }

    public function testDenormalizeNullField()
    {
        $object = new \stdClass();

        /** @var DenormalizerContextInterface|MockObject $context */
        $context = $this->getMockByCalls(DenormalizerContextInterface::class);

        /** @var FieldDenormalizerInterface|MockObject $fieldDenormalizer */
        $fieldDenormalizer = $this->getMockByCalls(FieldDenormalizerInterface::class, [
            Call::create('denormalizeField')->with('date', $object, null, $context, null),
        ]);

        $dateFieldDenormalizer = new DateFieldDenormalizer($fieldDenormalizer);
        $dateFieldDenormalizer->denormalizeField('date', $object, null, $context);
    }

    public function testDenormalizeNullStringField()
    {
        $object = new \stdClass();

        /** @var DenormalizerContextInterface|MockObject $context */
        $context = $this->getMockByCalls(DenormalizerContextInterface::class);

        /** @var FieldDenormalizerInterface|MockObject $fieldDenormalizer */
        $fieldDenormalizer = $this->getMockByCalls(FieldDenormalizerInterface::class, [
            Call::create('denormalizeField')->with('date', $object, 'null', $context, null),
        ]);

        $dateFieldDenormalizer = new DateFieldDenormalizer($fieldDenormalizer);
        $dateFieldDenormalizer->denormalizeField('date', $object, 'null', $context);
    }

    public function testDenormalizeZeroField()
    {
        $object = new \stdClass();

        /** @var DenormalizerContextInterface|MockObject $context */
        $context = $this->getMockByCalls(DenormalizerContextInterface::class);

        /** @var FieldDenormalizerInterface|MockObject $fieldDenormalizer */
        $fieldDenormalizer = $this->getMockByCalls(FieldDenormalizerInterface::class, [
            Call::create('denormalizeField')->with('date', $object, 0, $context, null),
        ]);

        $dateFieldDenormalizer = new DateFieldDenormalizer($fieldDenormalizer);
        $dateFieldDenormalizer->denormalizeField('date', $object, 0, $context);
    }

    public function testDenormalizeZeroStringField()
    {
        $object = new \stdClass();

        /** @var DenormalizerContextInterface|MockObject $context */
        $context = $this->getMockByCalls(DenormalizerContextInterface::class);

        /** @var FieldDenormalizerInterface|MockObject $fieldDenormalizer */
        $fieldDenormalizer = $this->getMockByCalls(FieldDenormalizerInterface::class, [
            Call::create('denormalizeField')->with('date', $object, '0', $context, null),
        ]);

        $dateFieldDenormalizer = new DateFieldDenormalizer($fieldDenormalizer);
        $dateFieldDenormalizer->denormalizeField('date', $object, '0', $context);
    }

    public function testDenormalizeArrayField()
    {
        $object = new \stdClass();

        /** @var DenormalizerContextInterface|MockObject $context */
        $context = $this->getMockByCalls(DenormalizerContextInterface::class);

        /** @var FieldDenormalizerInterface|MockObject $fieldDenormalizer */
        $fieldDenormalizer = $this->getMockByCalls(FieldDenormalizerInterface::class, [
            Call::create('denormalizeField')->with('date', $object, [], $context, null),
        ]);

        $dateFieldDenormalizer = new DateFieldDenormalizer($fieldDenormalizer);
        $dateFieldDenormalizer->denormalizeField('date', $object, [], $context);
    }

    public function testDenormalizeObjectField()
    {
        $object = new \stdClass();

        $date = new \stdClass();

        /** @var DenormalizerContextInterface|MockObject $context */
        $context = $this->getMockByCalls(DenormalizerContextInterface::class);

        /** @var FieldDenormalizerInterface|MockObject $fieldDenormalizer */
        $fieldDenormalizer = $this->getMockByCalls(FieldDenormalizerInterface::class, [
            Call::create('denormalizeField')->with('date', $object, $date, $context, null),
        ]);

        $dateFieldDenormalizer = new DateFieldDenormalizer($fieldDenormalizer);
        $dateFieldDenormalizer->denormalizeField('date', $object, $date, $context);
    }
}
