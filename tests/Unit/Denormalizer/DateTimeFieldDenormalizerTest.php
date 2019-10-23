<?php

declare(strict_types=1);

namespace Chubbyphp\Tests\Deserialization\Unit\Denormalizer;

use Chubbyphp\Deserialization\Accessor\AccessorInterface;
use Chubbyphp\Deserialization\Denormalizer\DateTimeFieldDenormalizer;
use Chubbyphp\Deserialization\Denormalizer\DenormalizerContextInterface;
use Chubbyphp\Deserialization\Denormalizer\FieldDenormalizerInterface;
use Chubbyphp\Mock\Argument\ArgumentCallback;
use Chubbyphp\Mock\Call;
use Chubbyphp\Mock\MockByCallsTrait;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Chubbyphp\Deserialization\Denormalizer\DateTimeFieldDenormalizer
 *
 * @internal
 */
final class DateTimeFieldDenormalizerTest extends TestCase
{
    use MockByCallsTrait;

    public function testDenormalizeFieldWithInvalidConstructArgument(): void
    {
        $this->expectException(\TypeError::class);
        $this->expectExceptionMessage('Chubbyphp\Deserialization\Denormalizer\DateTimeFieldDenormalizer::__construct() expects parameter 1 to be Chubbyphp\Deserialization\Accessor\AccessorInterface|Chubbyphp\Deserialization\Denormalizer\FieldDenormalizerInterface, DateTime given');

        new DateTimeFieldDenormalizer(new \DateTime());
    }

    public function testDenormalizeFieldWithFieldDenormalizer(): void
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
                        function ($date): void {
                            self::assertInstanceOf(\DateTime::class, $date);
                            self::assertSame('2017-01-01', $date->format('Y-m-d'));
                        }
                    ),
                    $context,
                    null
                ),
        ]);

        error_clear_last();

        $fieldDenormalizer = new DateTimeFieldDenormalizer($fieldDenormalizer);

        $error = error_get_last();

        self::assertNotNull($error);

        self::assertSame(E_USER_DEPRECATED, $error['type']);
        self::assertSame(
            sprintf(
                'Use "%s" instead of "%s" as __construct argument',
                AccessorInterface::class,
                FieldDenormalizerInterface::class
            ),
            $error['message']
        );

        $fieldDenormalizer->denormalizeField('date', $object, '2017-01-01', $context);
    }

    public function testDenormalizeField(): void
    {
        $object = new \stdClass();

        /** @var AccessorInterface|MockObject $accessor */
        $accessor = $this->getMockByCalls(AccessorInterface::class, [
            Call::create('setValue')->with($object, new ArgumentCallback(
                function ($value): void {
                    self::assertInstanceOf(\DateTime::class, $value);
                    self::assertSame('2017-01-01', $value->format('Y-m-d'));
                }
            )),
        ]);

        /** @var DenormalizerContextInterface|MockObject $context */
        $context = $this->getMockByCalls(DenormalizerContextInterface::class);

        $fieldDenormalizer = new DateTimeFieldDenormalizer($accessor);
        $fieldDenormalizer->denormalizeField('date', $object, '2017-01-01', $context);
    }

    public function testDenormalizeInvalidMonthField(): void
    {
        $object = new \stdClass();

        /** @var AccessorInterface|MockObject $accessor */
        $accessor = $this->getMockByCalls(AccessorInterface::class, [
            Call::create('setValue')->with($object, '2017-13-01'),
        ]);

        /** @var DenormalizerContextInterface|MockObject $context */
        $context = $this->getMockByCalls(DenormalizerContextInterface::class);

        $fieldDenormalizer = new DateTimeFieldDenormalizer($accessor);
        $fieldDenormalizer->denormalizeField('date', $object, '2017-13-01', $context);
    }

    public function testDenormalizeInvalidDayField(): void
    {
        $object = new \stdClass();

        /** @var AccessorInterface|MockObject $accessor */
        $accessor = $this->getMockByCalls(AccessorInterface::class, [
            Call::create('setValue')->with($object, '2017-02-31'),
        ]);

        /** @var DenormalizerContextInterface|MockObject $context */
        $context = $this->getMockByCalls(DenormalizerContextInterface::class);

        $fieldDenormalizer = new DateTimeFieldDenormalizer($accessor);
        $fieldDenormalizer->denormalizeField('date', $object, '2017-02-31', $context);
    }

    public function testDenormalizeInvalidWithAllZeroField(): void
    {
        $object = new \stdClass();

        /** @var AccessorInterface|MockObject $accessor */
        $accessor = $this->getMockByCalls(AccessorInterface::class, [
            Call::create('setValue')->with($object, '0000-00-00'),
        ]);

        /** @var DenormalizerContextInterface|MockObject $context */
        $context = $this->getMockByCalls(DenormalizerContextInterface::class);

        $fieldDenormalizer = new DateTimeFieldDenormalizer($accessor);
        $fieldDenormalizer->denormalizeField('date', $object, '0000-00-00', $context);
    }

    public function testDenormalizeEmptyField(): void
    {
        $object = new \stdClass();

        /** @var AccessorInterface|MockObject $accessor */
        $accessor = $this->getMockByCalls(AccessorInterface::class, [
            Call::create('setValue')->with($object, ''),
        ]);

        /** @var DenormalizerContextInterface|MockObject $context */
        $context = $this->getMockByCalls(DenormalizerContextInterface::class);

        $fieldDenormalizer = new DateTimeFieldDenormalizer($accessor);
        $fieldDenormalizer->denormalizeField('date', $object, '', $context);
    }

    public function testDenormalizeWhitespaceOnlyField(): void
    {
        $object = new \stdClass();

        /** @var AccessorInterface|MockObject $accessor */
        $accessor = $this->getMockByCalls(AccessorInterface::class, [
            Call::create('setValue')->with($object, '    '),
        ]);

        /** @var DenormalizerContextInterface|MockObject $context */
        $context = $this->getMockByCalls(DenormalizerContextInterface::class);

        $fieldDenormalizer = new DateTimeFieldDenormalizer($accessor);
        $fieldDenormalizer->denormalizeField('date', $object, '    ', $context);
    }

    public function testDenormalizeNullField(): void
    {
        $object = new \stdClass();

        /** @var AccessorInterface|MockObject $accessor */
        $accessor = $this->getMockByCalls(AccessorInterface::class, [
            Call::create('setValue')->with($object, null),
        ]);

        /** @var DenormalizerContextInterface|MockObject $context */
        $context = $this->getMockByCalls(DenormalizerContextInterface::class);

        $fieldDenormalizer = new DateTimeFieldDenormalizer($accessor);
        $fieldDenormalizer->denormalizeField('date', $object, null, $context);
    }

    public function testDenormalizeNullStringField(): void
    {
        $object = new \stdClass();

        /** @var AccessorInterface|MockObject $accessor */
        $accessor = $this->getMockByCalls(AccessorInterface::class, [
            Call::create('setValue')->with($object, 'null'),
        ]);

        /** @var DenormalizerContextInterface|MockObject $context */
        $context = $this->getMockByCalls(DenormalizerContextInterface::class);

        $fieldDenormalizer = new DateTimeFieldDenormalizer($accessor);
        $fieldDenormalizer->denormalizeField('date', $object, 'null', $context);
    }

    public function testDenormalizeZeroField(): void
    {
        $object = new \stdClass();

        /** @var AccessorInterface|MockObject $accessor */
        $accessor = $this->getMockByCalls(AccessorInterface::class, [
            Call::create('setValue')->with($object, 0),
        ]);

        /** @var DenormalizerContextInterface|MockObject $context */
        $context = $this->getMockByCalls(DenormalizerContextInterface::class);

        $fieldDenormalizer = new DateTimeFieldDenormalizer($accessor);
        $fieldDenormalizer->denormalizeField('date', $object, 0, $context);
    }

    public function testDenormalizeZeroStringField(): void
    {
        $object = new \stdClass();

        /** @var AccessorInterface|MockObject $accessor */
        $accessor = $this->getMockByCalls(AccessorInterface::class, [
            Call::create('setValue')->with($object, '0'),
        ]);

        /** @var DenormalizerContextInterface|MockObject $context */
        $context = $this->getMockByCalls(DenormalizerContextInterface::class);

        $fieldDenormalizer = new DateTimeFieldDenormalizer($accessor);

        error_clear_last();

        $fieldDenormalizer->denormalizeField('date', $object, '0', $context);

        $error = error_get_last();

        self::assertNull($error);
    }

    public function testDenormalizeArrayField(): void
    {
        $object = new \stdClass();

        /** @var AccessorInterface|MockObject $accessor */
        $accessor = $this->getMockByCalls(AccessorInterface::class, [
            Call::create('setValue')->with($object, []),
        ]);

        /** @var DenormalizerContextInterface|MockObject $context */
        $context = $this->getMockByCalls(DenormalizerContextInterface::class);

        $fieldDenormalizer = new DateTimeFieldDenormalizer($accessor);
        $fieldDenormalizer->denormalizeField('date', $object, [], $context);
    }

    public function testDenormalizeObjectField(): void
    {
        $object = new \stdClass();

        $date = new \stdClass();

        /** @var AccessorInterface|MockObject $accessor */
        $accessor = $this->getMockByCalls(AccessorInterface::class, [
            Call::create('setValue')->with($object, $date),
        ]);

        /** @var DenormalizerContextInterface|MockObject $context */
        $context = $this->getMockByCalls(DenormalizerContextInterface::class);

        $fieldDenormalizer = new DateTimeFieldDenormalizer($accessor);
        $fieldDenormalizer->denormalizeField('date', $object, $date, $context);
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

        $fieldDenormalizer = new DateTimeFieldDenormalizer($accessor);
        $fieldDenormalizer->denormalizeField('date', $object, '', $context);
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

        $fieldDenormalizer = new DateTimeFieldDenormalizer($accessor, true);
        $fieldDenormalizer->denormalizeField('date', $object, '', $context);
    }

    public function testDenormalizeFieldWithTimezone(): void
    {
        $object = new \stdClass();

        /** @var DenormalizerContextInterface|MockObject $context */
        $context = $this->getMockByCalls(DenormalizerContextInterface::class);

        /** @var FieldDenormalizerInterface|MockObject $fieldDenormalizer */
        $fieldDenormalizer = $this->getMockByCalls(AccessorInterface::class, [
            Call::create('setValue')->with($object, new ArgumentCallback(
                function ($value): void {
                    self::assertInstanceOf(\DateTime::class, $value);
                    self::assertSame('2016-12-31 22:00:00', $value->format('Y-m-d H:i:s'));
                }
            )),
        ]);

        $fieldDenormalizer = new DateTimeFieldDenormalizer($fieldDenormalizer, false, new \DateTimeZone('UTC'));
        $fieldDenormalizer->denormalizeField('date', $object, '2017-01-01T00:00:00+02:00', $context);
    }
}
