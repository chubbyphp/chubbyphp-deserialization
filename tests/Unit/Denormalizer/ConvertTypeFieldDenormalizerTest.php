<?php

declare(strict_types=1);

namespace Chubbyphp\Tests\Deserialization\Unit\Denormalizer;

use Chubbyphp\Deserialization\Accessor\AccessorInterface;
use Chubbyphp\Deserialization\Denormalizer\ConvertTypeFieldDenormalizer;
use Chubbyphp\Deserialization\Denormalizer\DenormalizerContextInterface;
use Chubbyphp\Deserialization\DeserializerLogicException;
use Chubbyphp\Mock\MockMethod\WithoutReturn;
use Chubbyphp\Mock\MockObjectBuilder;
use PHPUnit\Framework\Attributes\DoesNotPerformAssertions;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Chubbyphp\Deserialization\Denormalizer\ConvertTypeFieldDenormalizer
 *
 * @internal
 */
final class ConvertTypeFieldDenormalizerTest extends TestCase
{
    public function testDenormalizeFieldWithInvalidType(): void
    {
        $this->expectException(DeserializerLogicException::class);
        $this->expectExceptionMessage('Convert type "type" is not supported');

        $builder = new MockObjectBuilder();

        /** @var AccessorInterface $accessor */
        $accessor = $builder->create(AccessorInterface::class, []);

        new ConvertTypeFieldDenormalizer($accessor, 'type');
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

        $fieldDenormalizer = new ConvertTypeFieldDenormalizer($accessor, ConvertTypeFieldDenormalizer::TYPE_INT);
        $fieldDenormalizer->denormalizeField('value', $object, null, $context);
    }

    #[DoesNotPerformAssertions]
    public function testDenormalizeFieldWithArray(): void
    {
        $object = new \stdClass();
        $builder = new MockObjectBuilder();

        /** @var AccessorInterface $accessor */
        $accessor = $builder->create(AccessorInterface::class, [
            new WithoutReturn('setValue', [$object, []]),
        ]);

        /** @var DenormalizerContextInterface $context */
        $context = $builder->create(DenormalizerContextInterface::class, []);

        $fieldDenormalizer = new ConvertTypeFieldDenormalizer($accessor, ConvertTypeFieldDenormalizer::TYPE_INT);
        $fieldDenormalizer->denormalizeField('value', $object, [], $context);
    }

    #[DoesNotPerformAssertions]
    public function testDenormalizeFieldWithFloatWhichCantBeConvertedToBool(): void
    {
        $object = new \stdClass();
        $builder = new MockObjectBuilder();

        /** @var AccessorInterface $accessor */
        $accessor = $builder->create(AccessorInterface::class, [
            new WithoutReturn('setValue', [$object, 1.0]),
        ]);

        /** @var DenormalizerContextInterface $context */
        $context = $builder->create(DenormalizerContextInterface::class, []);

        $fieldDenormalizer = new ConvertTypeFieldDenormalizer($accessor, ConvertTypeFieldDenormalizer::TYPE_BOOL);
        $fieldDenormalizer->denormalizeField('value', $object, 1.0, $context);
    }

    #[DoesNotPerformAssertions]
    public function testDenormalizeFieldWithIntWhichCantBeConvertedToBool(): void
    {
        $object = new \stdClass();
        $builder = new MockObjectBuilder();

        /** @var AccessorInterface $accessor */
        $accessor = $builder->create(AccessorInterface::class, [
            new WithoutReturn('setValue', [$object, 1]),
        ]);

        /** @var DenormalizerContextInterface $context */
        $context = $builder->create(DenormalizerContextInterface::class, []);

        $fieldDenormalizer = new ConvertTypeFieldDenormalizer($accessor, ConvertTypeFieldDenormalizer::TYPE_BOOL);
        $fieldDenormalizer->denormalizeField('value', $object, 1, $context);
    }

    #[DoesNotPerformAssertions]
    public function testDenormalizeFieldWithStringConvertedToTrue(): void
    {
        $object = new \stdClass();
        $builder = new MockObjectBuilder();

        /** @var AccessorInterface $accessor */
        $accessor = $builder->create(AccessorInterface::class, [
            new WithoutReturn('setValue', [$object, true]),
        ]);

        /** @var DenormalizerContextInterface $context */
        $context = $builder->create(DenormalizerContextInterface::class, []);

        $fieldDenormalizer = new ConvertTypeFieldDenormalizer($accessor, ConvertTypeFieldDenormalizer::TYPE_BOOL);
        $fieldDenormalizer->denormalizeField('value', $object, 'true', $context);
    }

    #[DoesNotPerformAssertions]
    public function testDenormalizeFieldWithStringConvertedToFalse(): void
    {
        $object = new \stdClass();
        $builder = new MockObjectBuilder();

        /** @var AccessorInterface $accessor */
        $accessor = $builder->create(AccessorInterface::class, [
            new WithoutReturn('setValue', [$object, false]),
        ]);

        /** @var DenormalizerContextInterface $context */
        $context = $builder->create(DenormalizerContextInterface::class, []);

        $fieldDenormalizer = new ConvertTypeFieldDenormalizer($accessor, ConvertTypeFieldDenormalizer::TYPE_BOOL);
        $fieldDenormalizer->denormalizeField('value', $object, 'false', $context);
    }

    #[DoesNotPerformAssertions]
    public function testDenormalizeFieldWithStringWhichCantBeConvertedToBool(): void
    {
        $object = new \stdClass();
        $builder = new MockObjectBuilder();

        /** @var AccessorInterface $accessor */
        $accessor = $builder->create(AccessorInterface::class, [
            new WithoutReturn('setValue', [$object, 'test']),
        ]);

        /** @var DenormalizerContextInterface $context */
        $context = $builder->create(DenormalizerContextInterface::class, []);

        $fieldDenormalizer = new ConvertTypeFieldDenormalizer($accessor, ConvertTypeFieldDenormalizer::TYPE_BOOL);
        $fieldDenormalizer->denormalizeField('value', $object, 'test', $context);
    }

    #[DoesNotPerformAssertions]
    public function testDenormalizeFieldWithBoolWhichCantBeConvertedToFloat(): void
    {
        $object = new \stdClass();
        $builder = new MockObjectBuilder();

        /** @var AccessorInterface $accessor */
        $accessor = $builder->create(AccessorInterface::class, [
            new WithoutReturn('setValue', [$object, true]),
        ]);

        /** @var DenormalizerContextInterface $context */
        $context = $builder->create(DenormalizerContextInterface::class, []);

        $fieldDenormalizer = new ConvertTypeFieldDenormalizer($accessor, ConvertTypeFieldDenormalizer::TYPE_FLOAT);
        $fieldDenormalizer->denormalizeField('value', $object, true, $context);
    }

    #[DoesNotPerformAssertions]
    public function testDenormalizeFieldWithIntegerConvertedToFloat(): void
    {
        $object = new \stdClass();

        $builder = new MockObjectBuilder();

        /** @var AccessorInterface $accessor */
        $accessor = $builder->create(AccessorInterface::class, [
            new WithoutReturn('setValue', [$object, 1.0]),
        ]);

        /** @var DenormalizerContextInterface $context */
        $context = $builder->create(DenormalizerContextInterface::class, []);

        $fieldDenormalizer = new ConvertTypeFieldDenormalizer($accessor, ConvertTypeFieldDenormalizer::TYPE_FLOAT);
        $fieldDenormalizer->denormalizeField('value', $object, 1, $context);
    }

    #[DoesNotPerformAssertions]
    public function testDenormalizeFieldWithSpecialIntegerConvertedToFloat(): void
    {
        $object = new \stdClass();

        $builder = new MockObjectBuilder();

        /** @var AccessorInterface $accessor */
        $accessor = $builder->create(AccessorInterface::class, [
            new WithoutReturn('setValue', [$object, 1337.0]),
            new WithoutReturn('setValue', [$object, 1337.0]),
            new WithoutReturn('setValue', [$object, 1337.0]),
            new WithoutReturn('setValue', [$object, 1337.0]),
        ]);

        /** @var DenormalizerContextInterface $context */
        $context = $builder->create(DenormalizerContextInterface::class, []);

        $fieldDenormalizer = new ConvertTypeFieldDenormalizer($accessor, ConvertTypeFieldDenormalizer::TYPE_FLOAT);

        $fieldDenormalizer->denormalizeField('value', $object, 0x539, $context);
        $fieldDenormalizer->denormalizeField('value', $object, 0b10100111001, $context);
        $fieldDenormalizer->denormalizeField('value', $object, 0o2471, $context);
        $fieldDenormalizer->denormalizeField('value', $object, 1337e0, $context);
    }

    #[DoesNotPerformAssertions]
    public function testDenormalizeFieldWithStringWhichCantBeConvertedToFloat(): void
    {
        $object = new \stdClass();

        $builder = new MockObjectBuilder();

        /** @var AccessorInterface $accessor */
        $accessor = $builder->create(AccessorInterface::class, [
            new WithoutReturn('setValue', [$object, '5.5.5']),
        ]);

        /** @var DenormalizerContextInterface $context */
        $context = $builder->create(DenormalizerContextInterface::class, []);

        $fieldDenormalizer = new ConvertTypeFieldDenormalizer($accessor, ConvertTypeFieldDenormalizer::TYPE_FLOAT);
        $fieldDenormalizer->denormalizeField('value', $object, '5.5.5', $context);
    }

    #[DoesNotPerformAssertions]
    public function testDenormalizeFieldWithStringConvertedToFloat(): void
    {
        $object = new \stdClass();

        $builder = new MockObjectBuilder();

        /** @var AccessorInterface $accessor */
        $accessor = $builder->create(AccessorInterface::class, [
            new WithoutReturn('setValue', [$object, 5.5]),
        ]);

        /** @var DenormalizerContextInterface $context */
        $context = $builder->create(DenormalizerContextInterface::class, []);

        $fieldDenormalizer = new ConvertTypeFieldDenormalizer($accessor, ConvertTypeFieldDenormalizer::TYPE_FLOAT);
        $fieldDenormalizer->denormalizeField('value', $object, '5.500', $context);
    }

    #[DoesNotPerformAssertions]
    public function testDenormalizeFieldWithBoolWhichCantBeConvertedToInteger(): void
    {
        $object = new \stdClass();

        $builder = new MockObjectBuilder();

        /** @var AccessorInterface $accessor */
        $accessor = $builder->create(AccessorInterface::class, [
            new WithoutReturn('setValue', [$object, true]),
        ]);

        /** @var DenormalizerContextInterface $context */
        $context = $builder->create(DenormalizerContextInterface::class, []);

        $fieldDenormalizer = new ConvertTypeFieldDenormalizer($accessor, ConvertTypeFieldDenormalizer::TYPE_INT);
        $fieldDenormalizer->denormalizeField('value', $object, true, $context);
    }

    #[DoesNotPerformAssertions]
    public function testDenormalizeFieldWithFloatConvertedToInteger(): void
    {
        $object = new \stdClass();

        $builder = new MockObjectBuilder();

        /** @var AccessorInterface $accessor */
        $accessor = $builder->create(AccessorInterface::class, [
            new WithoutReturn('setValue', [$object, 5]),
        ]);

        /** @var DenormalizerContextInterface $context */
        $context = $builder->create(DenormalizerContextInterface::class, []);

        $fieldDenormalizer = new ConvertTypeFieldDenormalizer($accessor, ConvertTypeFieldDenormalizer::TYPE_INT);
        $fieldDenormalizer->denormalizeField('value', $object, 5.0, $context);
    }

    #[DoesNotPerformAssertions]
    public function testDenormalizeFieldWithFloatWhichCantBeConvertedToInteger(): void
    {
        $object = new \stdClass();

        $builder = new MockObjectBuilder();

        /** @var AccessorInterface $accessor */
        $accessor = $builder->create(AccessorInterface::class, [
            new WithoutReturn('setValue', [$object, 5.1]),
        ]);

        /** @var DenormalizerContextInterface $context */
        $context = $builder->create(DenormalizerContextInterface::class, []);

        $fieldDenormalizer = new ConvertTypeFieldDenormalizer($accessor, ConvertTypeFieldDenormalizer::TYPE_INT);
        $fieldDenormalizer->denormalizeField('value', $object, 5.1, $context);
    }

    #[DoesNotPerformAssertions]
    public function testDenormalizeFieldWithStringWhichCantBeConvertedToInteger(): void
    {
        $object = new \stdClass();

        $builder = new MockObjectBuilder();

        /** @var AccessorInterface $accessor */
        $accessor = $builder->create(AccessorInterface::class, [
            new WithoutReturn('setValue', [$object, 'test']),
        ]);

        /** @var DenormalizerContextInterface $context */
        $context = $builder->create(DenormalizerContextInterface::class, []);

        $fieldDenormalizer = new ConvertTypeFieldDenormalizer($accessor, ConvertTypeFieldDenormalizer::TYPE_INT);
        $fieldDenormalizer->denormalizeField('value', $object, 'test', $context);
    }

    #[DoesNotPerformAssertions]
    public function testDenormalizeFieldWithStringConvertedToInteger(): void
    {
        $object = new \stdClass();

        $builder = new MockObjectBuilder();

        /** @var AccessorInterface $accessor */
        $accessor = $builder->create(AccessorInterface::class, [
            new WithoutReturn('setValue', [$object, 5]),
        ]);

        /** @var DenormalizerContextInterface $context */
        $context = $builder->create(DenormalizerContextInterface::class, []);

        $fieldDenormalizer = new ConvertTypeFieldDenormalizer($accessor, ConvertTypeFieldDenormalizer::TYPE_INT);
        $fieldDenormalizer->denormalizeField('value', $object, '5', $context);
    }

    #[DoesNotPerformAssertions]
    public function testDenormalizeFieldWithBoolWhichCantBeConvertedToString(): void
    {
        $object = new \stdClass();

        $builder = new MockObjectBuilder();

        /** @var AccessorInterface $accessor */
        $accessor = $builder->create(AccessorInterface::class, [
            new WithoutReturn('setValue', [$object, true]),
        ]);

        /** @var DenormalizerContextInterface $context */
        $context = $builder->create(DenormalizerContextInterface::class, []);

        $fieldDenormalizer = new ConvertTypeFieldDenormalizer($accessor, ConvertTypeFieldDenormalizer::TYPE_STRING);
        $fieldDenormalizer->denormalizeField('value', $object, true, $context);
    }

    #[DoesNotPerformAssertions]
    public function testDenormalizeFieldWithFloatConvertedToString(): void
    {
        $object = new \stdClass();

        $builder = new MockObjectBuilder();

        /** @var AccessorInterface $accessor */
        $accessor = $builder->create(AccessorInterface::class, [
            new WithoutReturn('setValue', [$object, '5.5']),
        ]);

        /** @var DenormalizerContextInterface $context */
        $context = $builder->create(DenormalizerContextInterface::class, []);

        $fieldDenormalizer = new ConvertTypeFieldDenormalizer($accessor, ConvertTypeFieldDenormalizer::TYPE_STRING);
        $fieldDenormalizer->denormalizeField('value', $object, 5.5, $context);
    }

    #[DoesNotPerformAssertions]
    public function testDenormalizeFieldWithIntegerConvertedToString(): void
    {
        $object = new \stdClass();

        $builder = new MockObjectBuilder();

        /** @var AccessorInterface $accessor */
        $accessor = $builder->create(AccessorInterface::class, [
            new WithoutReturn('setValue', [$object, '5']),
        ]);

        /** @var DenormalizerContextInterface $context */
        $context = $builder->create(DenormalizerContextInterface::class, []);

        $fieldDenormalizer = new ConvertTypeFieldDenormalizer($accessor, ConvertTypeFieldDenormalizer::TYPE_STRING);
        $fieldDenormalizer->denormalizeField('value', $object, 5, $context);
    }

    #[DoesNotPerformAssertions]
    public function testDenormalizeFieldWithSpecialIntegerConvertedToString(): void
    {
        $object = new \stdClass();

        $builder = new MockObjectBuilder();

        /** @var AccessorInterface $accessor */
        $accessor = $builder->create(AccessorInterface::class, [
            new WithoutReturn('setValue', [$object, '1337']),
            new WithoutReturn('setValue', [$object, '1337']),
            new WithoutReturn('setValue', [$object, '1337']),
            new WithoutReturn('setValue', [$object, '1337']),
        ]);

        /** @var DenormalizerContextInterface $context */
        $context = $builder->create(DenormalizerContextInterface::class, []);

        $fieldDenormalizer = new ConvertTypeFieldDenormalizer($accessor, ConvertTypeFieldDenormalizer::TYPE_STRING);

        $fieldDenormalizer->denormalizeField('value', $object, 0x539, $context);
        $fieldDenormalizer->denormalizeField('value', $object, 0b10100111001, $context);
        $fieldDenormalizer->denormalizeField('value', $object, 0o2471, $context);
        $fieldDenormalizer->denormalizeField('value', $object, 1337e0, $context);
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

        /** @var DenormalizerContextInterface $context */
        $context = $builder->create(DenormalizerContextInterface::class, []);

        $fieldDenormalizer = new ConvertTypeFieldDenormalizer($accessor, ConvertTypeFieldDenormalizer::TYPE_FLOAT);
        $fieldDenormalizer->denormalizeField('value', $object, '', $context);
    }

    #[DoesNotPerformAssertions]
    public function testDenormalizeFieldWithObjectToString(): void
    {
        $object = new \stdClass();
        $valueObject = new \stdClass();

        $builder = new MockObjectBuilder();

        /** @var AccessorInterface $accessor */
        $accessor = $builder->create(AccessorInterface::class, [
            new WithoutReturn('setValue', [$object, $valueObject]),
        ]);

        /** @var DenormalizerContextInterface $context */
        $context = $builder->create(DenormalizerContextInterface::class, []);

        $fieldDenormalizer = new ConvertTypeFieldDenormalizer($accessor, ConvertTypeFieldDenormalizer::TYPE_STRING);
        $fieldDenormalizer->denormalizeField('value', $object, $valueObject, $context);
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

        $fieldDenormalizer = new ConvertTypeFieldDenormalizer($accessor, ConvertTypeFieldDenormalizer::TYPE_FLOAT, true);
        $fieldDenormalizer->denormalizeField('value', $object, '', $context);
    }
}
