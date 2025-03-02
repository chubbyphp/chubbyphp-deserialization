<?php

declare(strict_types=1);

namespace Chubbyphp\Tests\Deserialization\Unit\Denormalizer;

use Chubbyphp\Deserialization\Accessor\AccessorInterface;
use Chubbyphp\Deserialization\Denormalizer\DateTimeImmutableFieldDenormalizer;
use Chubbyphp\Deserialization\Denormalizer\DenormalizerContextInterface;
use Chubbyphp\Mock\MockMethod\WithCallback;
use Chubbyphp\Mock\MockMethod\WithoutReturn;
use Chubbyphp\Mock\MockObjectBuilder;
use PHPUnit\Framework\Attributes\DoesNotPerformAssertions;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Chubbyphp\Deserialization\Denormalizer\DateTimeImmutableFieldDenormalizer
 *
 * @internal
 */
final class DateTimeImmutableFieldDenormalizerTest extends TestCase
{
    public function testDenormalizeField(): void
    {
        $object = new \stdClass();
        $builder = new MockObjectBuilder();

        /** @var AccessorInterface $accessor */
        $accessor = $builder->create(AccessorInterface::class, [
            new WithCallback('setValue', static function ($object, $value): void {
                self::assertInstanceOf(\DateTimeImmutable::class, $value);
                self::assertSame('2017-01-01', $value->format('Y-m-d'));
            }),
        ]);

        /** @var DenormalizerContextInterface $context */
        $context = $builder->create(DenormalizerContextInterface::class, []);

        $fieldDenormalizer = new DateTimeImmutableFieldDenormalizer($accessor);
        $fieldDenormalizer->denormalizeField('date', $object, '2017-01-01', $context);
    }

    #[DoesNotPerformAssertions]
    public function testDenormalizeInvalidMonthField(): void
    {
        $object = new \stdClass();

        $builder = new MockObjectBuilder();

        /** @var AccessorInterface $accessor */
        $accessor = $builder->create(AccessorInterface::class, [
            new WithoutReturn('setValue', [$object, '2017-13-01']),
        ]);

        /** @var DenormalizerContextInterface $context */
        $context = $builder->create(DenormalizerContextInterface::class, []);

        $fieldDenormalizer = new DateTimeImmutableFieldDenormalizer($accessor);
        $fieldDenormalizer->denormalizeField('date', $object, '2017-13-01', $context);
    }

    #[DoesNotPerformAssertions]
    public function testDenormalizeInvalidDayField(): void
    {
        $object = new \stdClass();

        $builder = new MockObjectBuilder();

        /** @var AccessorInterface $accessor */
        $accessor = $builder->create(AccessorInterface::class, [
            new WithoutReturn('setValue', [$object, '2017-02-31']),
        ]);

        /** @var DenormalizerContextInterface $context */
        $context = $builder->create(DenormalizerContextInterface::class, []);

        $fieldDenormalizer = new DateTimeImmutableFieldDenormalizer($accessor);
        $fieldDenormalizer->denormalizeField('date', $object, '2017-02-31', $context);
    }

    #[DoesNotPerformAssertions]
    public function testDenormalizeInvalidWithAllZeroField(): void
    {
        $object = new \stdClass();

        $builder = new MockObjectBuilder();

        /** @var AccessorInterface $accessor */
        $accessor = $builder->create(AccessorInterface::class, [
            new WithoutReturn('setValue', [$object, '0000-00-00']),
        ]);

        /** @var DenormalizerContextInterface $context */
        $context = $builder->create(DenormalizerContextInterface::class, []);

        $fieldDenormalizer = new DateTimeImmutableFieldDenormalizer($accessor);
        $fieldDenormalizer->denormalizeField('date', $object, '0000-00-00', $context);
    }

    #[DoesNotPerformAssertions]
    public function testDenormalizeEmptyField(): void
    {
        $object = new \stdClass();

        $builder = new MockObjectBuilder();

        /** @var AccessorInterface $accessor */
        $accessor = $builder->create(AccessorInterface::class, [
            new WithoutReturn('setValue', [$object, '']),
        ]);

        /** @var DenormalizerContextInterface $context */
        $context = $builder->create(DenormalizerContextInterface::class, []);

        $fieldDenormalizer = new DateTimeImmutableFieldDenormalizer($accessor);
        $fieldDenormalizer->denormalizeField('date', $object, '', $context);
    }

    #[DoesNotPerformAssertions]
    public function testDenormalizeWhitespaceOnlyField(): void
    {
        $object = new \stdClass();

        $builder = new MockObjectBuilder();

        /** @var AccessorInterface $accessor */
        $accessor = $builder->create(AccessorInterface::class, [
            new WithoutReturn('setValue', [$object, '    ']),
        ]);

        /** @var DenormalizerContextInterface $context */
        $context = $builder->create(DenormalizerContextInterface::class, []);

        $fieldDenormalizer = new DateTimeImmutableFieldDenormalizer($accessor);
        $fieldDenormalizer->denormalizeField('date', $object, '    ', $context);
    }

    #[DoesNotPerformAssertions]
    public function testDenormalizeNullField(): void
    {
        $object = new \stdClass();

        $builder = new MockObjectBuilder();

        /** @var AccessorInterface $accessor */
        $accessor = $builder->create(AccessorInterface::class, [
            new WithoutReturn('setValue', [$object, null]),
        ]);

        /** @var DenormalizerContextInterface $context */
        $context = $builder->create(DenormalizerContextInterface::class, []);

        $fieldDenormalizer = new DateTimeImmutableFieldDenormalizer($accessor);
        $fieldDenormalizer->denormalizeField('date', $object, null, $context);
    }

    #[DoesNotPerformAssertions]
    public function testDenormalizeNullStringField(): void
    {
        $object = new \stdClass();

        $builder = new MockObjectBuilder();

        /** @var AccessorInterface $accessor */
        $accessor = $builder->create(AccessorInterface::class, [
            new WithoutReturn('setValue', [$object, 'null']),
        ]);

        /** @var DenormalizerContextInterface $context */
        $context = $builder->create(DenormalizerContextInterface::class, []);

        $fieldDenormalizer = new DateTimeImmutableFieldDenormalizer($accessor);
        $fieldDenormalizer->denormalizeField('date', $object, 'null', $context);
    }

    #[DoesNotPerformAssertions]
    public function testDenormalizeZeroField(): void
    {
        $object = new \stdClass();

        $builder = new MockObjectBuilder();

        /** @var AccessorInterface $accessor */
        $accessor = $builder->create(AccessorInterface::class, [
            new WithoutReturn('setValue', [$object, 0]),
        ]);

        /** @var DenormalizerContextInterface $context */
        $context = $builder->create(DenormalizerContextInterface::class, []);

        $fieldDenormalizer = new DateTimeImmutableFieldDenormalizer($accessor);
        $fieldDenormalizer->denormalizeField('date', $object, 0, $context);
    }

    #[DoesNotPerformAssertions]
    public function testDenormalizeZeroStringField(): void
    {
        $object = new \stdClass();

        $builder = new MockObjectBuilder();

        /** @var AccessorInterface $accessor */
        $accessor = $builder->create(AccessorInterface::class, [
            new WithoutReturn('setValue', [$object, '0']),
        ]);

        /** @var DenormalizerContextInterface $context */
        $context = $builder->create(DenormalizerContextInterface::class, []);

        $fieldDenormalizer = new DateTimeImmutableFieldDenormalizer($accessor);
        $fieldDenormalizer->denormalizeField('date', $object, '0', $context);
    }

    #[DoesNotPerformAssertions]
    public function testDenormalizeArrayField(): void
    {
        $object = new \stdClass();

        $builder = new MockObjectBuilder();

        /** @var AccessorInterface $accessor */
        $accessor = $builder->create(AccessorInterface::class, [
            new WithoutReturn('setValue', [$object, []]),
        ]);

        /** @var DenormalizerContextInterface $context */
        $context = $builder->create(DenormalizerContextInterface::class, []);

        $fieldDenormalizer = new DateTimeImmutableFieldDenormalizer($accessor);
        $fieldDenormalizer->denormalizeField('date', $object, [], $context);
    }

    #[DoesNotPerformAssertions]
    public function testDenormalizeObjectField(): void
    {
        $object = new \stdClass();

        $date = new \stdClass();

        $builder = new MockObjectBuilder();

        /** @var AccessorInterface $accessor */
        $accessor = $builder->create(AccessorInterface::class, [
            new WithoutReturn('setValue', [$object, $date]),
        ]);

        /** @var DenormalizerContextInterface $context */
        $context = $builder->create(DenormalizerContextInterface::class, []);

        $fieldDenormalizer = new DateTimeImmutableFieldDenormalizer($accessor);
        $fieldDenormalizer->denormalizeField('date', $object, $date, $context);
    }

    #[DoesNotPerformAssertions]
    public function testDenormalizeFieldWithEmptyToNullDisabled(): void
    {
        $object = new \stdClass();

        $builder = new MockObjectBuilder();

        /** @var AccessorInterface $accessor */
        $accessor = $builder->create(AccessorInterface::class, [
            new WithoutReturn('setValue', [$object, '']),
        ]);

        $context = $builder->create(DenormalizerContextInterface::class, []);

        $fieldDenormalizer = new DateTimeImmutableFieldDenormalizer($accessor);
        $fieldDenormalizer->denormalizeField('date', $object, '', $context);
    }

    #[DoesNotPerformAssertions]
    public function testDenormalizeFieldWithEmptyToNullEnabled(): void
    {
        $object = new \stdClass();

        $builder = new MockObjectBuilder();

        /** @var AccessorInterface $accessor */
        $accessor = $builder->create(AccessorInterface::class, [
            new WithoutReturn('setValue', [$object, null]),
        ]);

        $context = $builder->create(DenormalizerContextInterface::class, []);

        $fieldDenormalizer = new DateTimeImmutableFieldDenormalizer($accessor, true);
        $fieldDenormalizer->denormalizeField('date', $object, '', $context);
    }

    public function testDenormalizeFieldWithTimezone(): void
    {
        $object = new \stdClass();

        $builder = new MockObjectBuilder();

        /** @var DenormalizerContextInterface $context */
        $context = $builder->create(DenormalizerContextInterface::class, []);

        /** @var AccessorInterface $accessor */
        $accessor = $builder->create(AccessorInterface::class, [
            new WithCallback('setValue', static function ($object, $value): void {
                self::assertInstanceOf(\DateTimeImmutable::class, $value);
                self::assertSame('2016-12-31 23:00:00', $value->format('Y-m-d H:i:s'));
            }),
        ]);

        $fieldDenormalizer = new DateTimeImmutableFieldDenormalizer(
            $accessor,
            false,
            new \DateTimeZone('Europe/Zurich')
        );
        $fieldDenormalizer->denormalizeField('date', $object, '2017-01-01T00:00:00+02:00', $context);
    }
}
