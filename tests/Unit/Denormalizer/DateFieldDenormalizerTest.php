<?php

declare(strict_types=1);

namespace Chubbyphp\Tests\Deserialization\Unit\Denormalizer;

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
 *
 * @internal
 */
final class DateFieldDenormalizerTest extends TestCase
{
    use MockByCallsTrait;

    public function testDenormalizeField(): void
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
                        function ($value): void {
                            self::assertInstanceOf(\DateTime::class, $value);
                            self::assertSame('2017-01-01', $value->format('Y-m-d'));
                        }
                    ),
                    $context,
                    null
                ),
        ]);

        $dateFieldDenormalizer = new DateFieldDenormalizer($fieldDenormalizer);

        error_clear_last();

        $dateFieldDenormalizer->denormalizeField('date', $object, '2017-01-01', $context);

        $error = error_get_last();

        self::assertNotNull($error);

        self::assertSame(E_USER_DEPRECATED, $error['type']);
        self::assertSame('Use Chubbyphp\Deserialization\Denormalizer\DateTimeFieldDenormalizer instead', $error['message']);
    }

    public function testDenormalizeInvalidMonthField(): void
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

    public function testDenormalizeInvalidDayField(): void
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

    public function testDenormalizeInvalidWithAllZeroField(): void
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

    public function testDenormalizeEmptyField(): void
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

    public function testDenormalizeWhitespaceOnlyField(): void
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

    public function testDenormalizeNullField(): void
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

    public function testDenormalizeNullStringField(): void
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

    public function testDenormalizeZeroField(): void
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

    public function testDenormalizeZeroStringField(): void
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

    public function testDenormalizeArrayField(): void
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

    public function testDenormalizeObjectField(): void
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
