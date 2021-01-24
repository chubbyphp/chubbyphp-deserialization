<?php

declare(strict_types=1);

namespace Chubbyphp\Tests\Deserialization\Unit\Denormalizer;

use Chubbyphp\Deserialization\Accessor\AccessorInterface;
use Chubbyphp\Deserialization\Denormalizer\DenormalizerContextInterface;
use Chubbyphp\Deserialization\Denormalizer\Relation\ReferenceOneFieldDenormalizer;
use Chubbyphp\Deserialization\DeserializerRuntimeException;
use Chubbyphp\Mock\Call;
use Chubbyphp\Mock\MockByCallsTrait;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Chubbyphp\Deserialization\Denormalizer\Relation\ReferenceOneFieldDenormalizer
 *
 * @internal
 */
final class ReferenceOneFieldDenormalizerTest extends TestCase
{
    use MockByCallsTrait;

    public function testDenormalizeFieldWithWrongType(): void
    {
        $this->expectException(DeserializerRuntimeException::class);
        $this->expectExceptionMessage('There is an invalid data type "integer", needed "string" at path: "reference"');

        $object = new \stdClass();

        /** @var AccessorInterface|MockObject $accessor */
        $accessor = $this->getMockByCalls(AccessorInterface::class);

        /** @var DenormalizerContextInterface|MockObject $context */
        $context = $this->getMockByCalls(DenormalizerContextInterface::class);

        $fieldDenormalizer = new ReferenceOneFieldDenormalizer(static function (string $id): void {}, $accessor);
        $fieldDenormalizer->denormalizeField('reference', $object, 5, $context);
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

        $fieldDenormalizer = new ReferenceOneFieldDenormalizer(static function (string $id): void {}, $accessor);
        $fieldDenormalizer->denormalizeField('reference', $object, null, $context);
    }

    public function testDenormalizeField(): void
    {
        $object = new \stdClass();

        $reference = new \stdClass();

        /** @var AccessorInterface|MockObject $accessor */
        $accessor = $this->getMockByCalls(AccessorInterface::class, [
            Call::create('setValue')->with($object, $reference),
        ]);

        /** @var DenormalizerContextInterface|MockObject $context */
        $context = $this->getMockByCalls(DenormalizerContextInterface::class);

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

        /** @var AccessorInterface|MockObject $accessor */
        $accessor = $this->getMockByCalls(AccessorInterface::class, [
            Call::create('setValue')->with($object, '60a9ee14-64d6-4992-8042-8d1528ac02d6'),
        ]);

        /** @var DenormalizerContextInterface|MockObject $context */
        $context = $this->getMockByCalls(DenormalizerContextInterface::class);

        $fieldDenormalizer = new ReferenceOneFieldDenormalizer(
            static function (string $id) {
                self::assertSame('60a9ee14-64d6-4992-8042-8d1528ac02d6', $id);

                return null;
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

        /** @var AccessorInterface|MockObject $accessor */
        $accessor = $this->getMockByCalls(AccessorInterface::class, [
            Call::create('setValue')->with($object, ''),
        ]);

        /** @var DenormalizerContextInterface|MockObject $context */
        $context = $this->getMockByCalls(DenormalizerContextInterface::class);

        $fieldDenormalizer = new ReferenceOneFieldDenormalizer(
            static function (string $id) {
                self::assertSame('', $id);

                return null;
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

    public function testDenormalizeFieldWithEmptyToNullEnabled(): void
    {
        $object = new \stdClass();

        /** @var AccessorInterface|MockObject $accessor */
        $accessor = $this->getMockByCalls(AccessorInterface::class, [
            Call::create('setValue')->with($object, null),
        ]);

        /** @var DenormalizerContextInterface|MockObject $context */
        $context = $this->getMockByCalls(DenormalizerContextInterface::class);

        $fieldDenormalizer = new ReferenceOneFieldDenormalizer(
            static function ($id) {
                self::assertSame(null, $id);

                return null;
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
