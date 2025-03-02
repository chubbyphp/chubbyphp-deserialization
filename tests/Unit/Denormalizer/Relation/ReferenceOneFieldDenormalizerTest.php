<?php

declare(strict_types=1);

namespace Chubbyphp\Tests\Deserialization\Unit\Denormalizer;

use Chubbyphp\Deserialization\Accessor\AccessorInterface;
use Chubbyphp\Deserialization\Denormalizer\DenormalizerContextInterface;
use Chubbyphp\Deserialization\Denormalizer\Relation\ReferenceOneFieldDenormalizer;
use Chubbyphp\Deserialization\DeserializerRuntimeException;
use Chubbyphp\Mock\MockMethod\WithoutReturn;
use Chubbyphp\Mock\MockObjectBuilder;
use PHPUnit\Framework\Attributes\DoesNotPerformAssertions;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Chubbyphp\Deserialization\Denormalizer\Relation\ReferenceOneFieldDenormalizer
 *
 * @internal
 */
final class ReferenceOneFieldDenormalizerTest extends TestCase
{
    public function testDenormalizeFieldWithWrongType(): void
    {
        $this->expectException(DeserializerRuntimeException::class);
        $this->expectExceptionMessage('There is an invalid data type "integer", needed "string" at path: "reference"');

        $object = new \stdClass();
        $builder = new MockObjectBuilder();

        /** @var AccessorInterface $accessor */
        $accessor = $builder->create(AccessorInterface::class, []);

        /** @var DenormalizerContextInterface $context */
        $context = $builder->create(DenormalizerContextInterface::class, []);

        $fieldDenormalizer = new ReferenceOneFieldDenormalizer(static function (string $id): void {}, $accessor);
        $fieldDenormalizer->denormalizeField('reference', $object, 5, $context);
    }

    #[DoesNotPerformAssertions]
    public function testDenormalizeFieldWithNull(): void
    {
        $object = new \stdClass();
        $builder = new MockObjectBuilder();

        /** @var AccessorInterface $accessor */
        $accessor = $builder->create(AccessorInterface::class, [
            new WithoutReturn('setValue', [$object, null]),
        ]);

        /** @var DenormalizerContextInterface $context */
        $context = $builder->create(DenormalizerContextInterface::class, []);

        $fieldDenormalizer = new ReferenceOneFieldDenormalizer(static function (string $id): void {}, $accessor);
        $fieldDenormalizer->denormalizeField('reference', $object, null, $context);
    }

    public function testDenormalizeField(): void
    {
        $object = new \stdClass();

        $reference = new \stdClass();
        $builder = new MockObjectBuilder();

        /** @var AccessorInterface $accessor */
        $accessor = $builder->create(AccessorInterface::class, [
            new WithoutReturn('setValue', [$object, $reference]),
        ]);

        /** @var DenormalizerContextInterface $context */
        $context = $builder->create(DenormalizerContextInterface::class, []);

        $fieldDenormalizer = new ReferenceOneFieldDenormalizer(
            static function (string $id) use ($reference) {
                self::assertSame('60a9ee14-64d6-4992-8042-8d1528ac02d6', $id);

                return $reference;
            },
            $accessor
        );

        $fieldDenormalizer->denormalizeField(
            'reference',
            $object,
            '60a9ee14-64d6-4992-8042-8d1528ac02d6',
            $context
        );
    }

    public function testDenormalizeFieldWithNotFoundValue(): void
    {
        $object = new \stdClass();
        $builder = new MockObjectBuilder();

        /** @var AccessorInterface $accessor */
        $accessor = $builder->create(AccessorInterface::class, [
            new WithoutReturn('setValue', [$object, '60a9ee14-64d6-4992-8042-8d1528ac02d6']),
        ]);

        /** @var DenormalizerContextInterface $context */
        $context = $builder->create(DenormalizerContextInterface::class, []);

        $fieldDenormalizer = new ReferenceOneFieldDenormalizer(
            static function (string $id): void {
                self::assertSame('60a9ee14-64d6-4992-8042-8d1528ac02d6', $id);
            },
            $accessor
        );

        $fieldDenormalizer->denormalizeField(
            'reference',
            $object,
            '60a9ee14-64d6-4992-8042-8d1528ac02d6',
            $context
        );
    }

    public function testDenormalizeFieldWithEmptyToNullDisabled(): void
    {
        $object = new \stdClass();
        $builder = new MockObjectBuilder();

        /** @var AccessorInterface $accessor */
        $accessor = $builder->create(AccessorInterface::class, [
            new WithoutReturn('setValue', [$object, '']),
        ]);

        /** @var DenormalizerContextInterface $context */
        $context = $builder->create(DenormalizerContextInterface::class, []);

        $fieldDenormalizer = new ReferenceOneFieldDenormalizer(
            static function (string $id): void {
                self::assertSame('', $id);
            },
            $accessor
        );

        $fieldDenormalizer->denormalizeField(
            'reference',
            $object,
            '',
            $context
        );
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

        /** @var DenormalizerContextInterface $context */
        $context = $builder->create(DenormalizerContextInterface::class, []);

        $fieldDenormalizer = new ReferenceOneFieldDenormalizer(
            static function ($id): void {
                self::assertNull($id);
            },
            $accessor,
            true
        );

        $fieldDenormalizer->denormalizeField(
            'reference',
            $object,
            '',
            $context
        );
    }
}
