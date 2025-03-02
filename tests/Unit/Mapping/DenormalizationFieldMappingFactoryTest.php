<?php

declare(strict_types=1);

namespace Chubbyphp\Tests\Deserialization\Unit\Mapping;

use Chubbyphp\Deserialization\Denormalizer\CallbackFieldDenormalizer;
use Chubbyphp\Deserialization\Denormalizer\ConvertTypeFieldDenormalizer;
use Chubbyphp\Deserialization\Denormalizer\DateTimeImmutableFieldDenormalizer;
use Chubbyphp\Deserialization\Denormalizer\FieldDenormalizer;
use Chubbyphp\Deserialization\Denormalizer\FieldDenormalizerInterface;
use Chubbyphp\Deserialization\Denormalizer\Relation\EmbedManyFieldDenormalizer;
use Chubbyphp\Deserialization\Denormalizer\Relation\EmbedOneFieldDenormalizer;
use Chubbyphp\Deserialization\Denormalizer\Relation\ReferenceManyFieldDenormalizer;
use Chubbyphp\Deserialization\Denormalizer\Relation\ReferenceOneFieldDenormalizer;
use Chubbyphp\Deserialization\Mapping\DenormalizationFieldMappingFactory;
use Chubbyphp\Deserialization\Policy\NullPolicy;
use Chubbyphp\Deserialization\Policy\PolicyInterface;
use Chubbyphp\Mock\MockObjectBuilder;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Chubbyphp\Deserialization\Mapping\DenormalizationFieldMappingFactory
 *
 * @internal
 */
final class DenormalizationFieldMappingFactoryTest extends TestCase
{
    private DenormalizationFieldMappingFactory $factory;

    protected function setUp(): void
    {
        $this->factory = new DenormalizationFieldMappingFactory();
    }

    public function testGetMappingWithDenormalizer(): void
    {
        $builder = new MockObjectBuilder();

        /** @var FieldDenormalizerInterface $fieldDenormalizer */
        $fieldDenormalizer = $builder->create(FieldDenormalizerInterface::class, []);

        $fieldMapping = $this->factory->create('name', false, $fieldDenormalizer);

        self::assertSame('name', $fieldMapping->getName());

        self::assertSame($fieldDenormalizer, $fieldMapping->getFieldDenormalizer());

        self::assertInstanceOf(NullPolicy::class, $fieldMapping->getPolicy());
    }

    public function testGetDefaultMapping(): void
    {
        $fieldMapping = $this->factory->create('name');

        self::assertSame('name', $fieldMapping->getName());

        $fieldDenormalizer = $fieldMapping->getFieldDenormalizer();

        self::assertInstanceOf(FieldDenormalizer::class, $fieldDenormalizer);

        $reflectionObject = new \ReflectionProperty($fieldDenormalizer, 'emptyToNull');
        $reflectionObject->setAccessible(true);

        self::assertFalse($reflectionObject->getValue($fieldDenormalizer));

        self::assertInstanceOf(NullPolicy::class, $fieldMapping->getPolicy());
    }

    public function testGetDefaultMappingWithEmptyToNull(): void
    {
        $fieldMapping = $this->factory->create('name', true);

        self::assertSame('name', $fieldMapping->getName());

        $fieldDenormalizer = $fieldMapping->getFieldDenormalizer();

        self::assertInstanceOf(FieldDenormalizer::class, $fieldDenormalizer);

        $reflectionObject = new \ReflectionProperty($fieldDenormalizer, 'emptyToNull');
        $reflectionObject->setAccessible(true);

        self::assertTrue($reflectionObject->getValue($fieldDenormalizer));

        self::assertInstanceOf(NullPolicy::class, $fieldMapping->getPolicy());
    }

    public function testGetDefaultMappingForCallback(): void
    {
        $fieldMapping = $this->factory->createCallback('name', static function (): void {});

        self::assertSame('name', $fieldMapping->getName());
        self::assertInstanceOf(CallbackFieldDenormalizer::class, $fieldMapping->getFieldDenormalizer());

        self::assertInstanceOf(NullPolicy::class, $fieldMapping->getPolicy());
    }

    public function testGetDefaultMappingForConvertType(): void
    {
        $fieldMapping = $this->factory->createConvertType('name', ConvertTypeFieldDenormalizer::TYPE_FLOAT);

        self::assertSame('name', $fieldMapping->getName());

        $fieldDenormalizer = $fieldMapping->getFieldDenormalizer();

        self::assertInstanceOf(ConvertTypeFieldDenormalizer::class, $fieldDenormalizer);

        $reflectionObject = new \ReflectionProperty($fieldDenormalizer, 'emptyToNull');
        $reflectionObject->setAccessible(true);

        self::assertFalse($reflectionObject->getValue($fieldDenormalizer));

        self::assertInstanceOf(NullPolicy::class, $fieldMapping->getPolicy());
    }

    public function testGetDefaultMappingForDateTimeImmutable(): void
    {
        $fieldMapping = $this->factory->createDateTimeImmutable('name');

        self::assertSame('name', $fieldMapping->getName());

        $fieldDenormalizer = $fieldMapping->getFieldDenormalizer();

        self::assertInstanceOf(DateTimeImmutableFieldDenormalizer::class, $fieldDenormalizer);

        $reflectionObject = new \ReflectionProperty($fieldDenormalizer, 'emptyToNull');
        $reflectionObject->setAccessible(true);

        self::assertFalse($reflectionObject->getValue($fieldDenormalizer));

        self::assertInstanceOf(NullPolicy::class, $fieldMapping->getPolicy());
    }

    public function testGetDefaultMappingForDateTimeImmutableWithEmptyToNull(): void
    {
        $fieldMapping = $this->factory->createDateTimeImmutable('name', true);

        self::assertSame('name', $fieldMapping->getName());

        $fieldDenormalizer = $fieldMapping->getFieldDenormalizer();

        self::assertInstanceOf(DateTimeImmutableFieldDenormalizer::class, $fieldDenormalizer);

        $reflectionObject = new \ReflectionProperty($fieldDenormalizer, 'emptyToNull');
        $reflectionObject->setAccessible(true);

        self::assertTrue($reflectionObject->getValue($fieldDenormalizer));

        self::assertInstanceOf(NullPolicy::class, $fieldMapping->getPolicy());
    }

    public function testGetDefaultMappingForDateTimeImmutableWithTimezone(): void
    {
        $fieldMapping = $this->factory->createDateTimeImmutable('name', false, new \DateTimeZone('UTC'));

        self::assertSame('name', $fieldMapping->getName());
        self::assertInstanceOf(DateTimeImmutableFieldDenormalizer::class, $fieldMapping->getFieldDenormalizer());

        self::assertInstanceOf(NullPolicy::class, $fieldMapping->getPolicy());
    }

    public function testGetDefaultMappingForEmbedMany(): void
    {
        $fieldMapping = $this->factory->createEmbedMany('name', \stdClass::class);

        self::assertSame('name', $fieldMapping->getName());
        self::assertInstanceOf(EmbedManyFieldDenormalizer::class, $fieldMapping->getFieldDenormalizer());

        self::assertInstanceOf(NullPolicy::class, $fieldMapping->getPolicy());
    }

    public function testGetDefaultMappingForEmbedOne(): void
    {
        $fieldMapping = $this->factory->createEmbedOne('name', \stdClass::class);

        self::assertSame('name', $fieldMapping->getName());
        self::assertInstanceOf(EmbedOneFieldDenormalizer::class, $fieldMapping->getFieldDenormalizer());

        self::assertInstanceOf(NullPolicy::class, $fieldMapping->getPolicy());
    }

    public function testGetDefaultMappingForReferenceMany(): void
    {
        $fieldMapping = $this->factory->createReferenceMany('name', static function (): void {});

        self::assertSame('name', $fieldMapping->getName());
        self::assertInstanceOf(ReferenceManyFieldDenormalizer::class, $fieldMapping->getFieldDenormalizer());

        self::assertInstanceOf(NullPolicy::class, $fieldMapping->getPolicy());
    }

    public function testGetDefaultMappingForReferenceOne(): void
    {
        $fieldMapping = $this->factory->createReferenceOne('name', static function (): void {});

        self::assertSame('name', $fieldMapping->getName());

        $fieldDenormalizer = $fieldMapping->getFieldDenormalizer();

        self::assertInstanceOf(ReferenceOneFieldDenormalizer::class, $fieldDenormalizer);

        $reflectionObject = new \ReflectionProperty($fieldDenormalizer, 'emptyToNull');
        $reflectionObject->setAccessible(true);

        self::assertFalse($reflectionObject->getValue($fieldDenormalizer));

        self::assertInstanceOf(NullPolicy::class, $fieldMapping->getPolicy());
    }

    public function testGetDefaultMappingForReferenceOneWithEmptyToNull(): void
    {
        $fieldMapping = $this->factory->createReferenceOne(
            'name',
            static function (): void {},
            true
        );

        self::assertSame('name', $fieldMapping->getName());

        $fieldDenormalizer = $fieldMapping->getFieldDenormalizer();

        self::assertInstanceOf(ReferenceOneFieldDenormalizer::class, $fieldDenormalizer);

        $reflectionObject = new \ReflectionProperty($fieldDenormalizer, 'emptyToNull');
        $reflectionObject->setAccessible(true);

        self::assertTrue($reflectionObject->getValue($fieldDenormalizer));

        self::assertInstanceOf(NullPolicy::class, $fieldMapping->getPolicy());
    }

    public function testGetMapping(): void
    {
        $builder = new MockObjectBuilder();

        /** @var FieldDenormalizerInterface $fieldDenormalizer */
        $fieldDenormalizer = $builder->create(FieldDenormalizerInterface::class, []);

        /** @var PolicyInterface $policy */
        $policy = $builder->create(PolicyInterface::class, []);

        $fieldMapping = $this->factory->create('name', false, $fieldDenormalizer, $policy);

        self::assertSame('name', $fieldMapping->getName());
        self::assertSame($fieldDenormalizer, $fieldMapping->getFieldDenormalizer());

        self::assertSame($policy, $fieldMapping->getPolicy());
    }
}
