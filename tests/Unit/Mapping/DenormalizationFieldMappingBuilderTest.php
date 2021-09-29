<?php

declare(strict_types=1);

namespace Chubbyphp\Tests\Deserialization\Unit\Mapping;

use Chubbyphp\Deserialization\Denormalizer\CallbackFieldDenormalizer;
use Chubbyphp\Deserialization\Denormalizer\ConvertTypeFieldDenormalizer;
use Chubbyphp\Deserialization\Denormalizer\DateTimeFieldDenormalizer;
use Chubbyphp\Deserialization\Denormalizer\FieldDenormalizer;
use Chubbyphp\Deserialization\Denormalizer\FieldDenormalizerInterface;
use Chubbyphp\Deserialization\Denormalizer\Relation\EmbedManyFieldDenormalizer;
use Chubbyphp\Deserialization\Denormalizer\Relation\EmbedOneFieldDenormalizer;
use Chubbyphp\Deserialization\Denormalizer\Relation\ReferenceManyFieldDenormalizer;
use Chubbyphp\Deserialization\Denormalizer\Relation\ReferenceOneFieldDenormalizer;
use Chubbyphp\Deserialization\Mapping\DenormalizationFieldMappingBuilder;
use Chubbyphp\Deserialization\Mapping\DenormalizationFieldMappingFactory;
use Chubbyphp\Deserialization\Policy\NullPolicy;
use Chubbyphp\Deserialization\Policy\PolicyInterface;
use Chubbyphp\Mock\MockByCallsTrait;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Chubbyphp\Deserialization\Mapping\DenormalizationFieldMappingBuilder
 *
 * @internal
 */
final class DenormalizationFieldMappingBuilderTest extends TestCase
{
    use MockByCallsTrait;

    public function testGetMappingWithDenormalizer(): void
    {
        /** @var FieldDenormalizerInterface|MockObject $fieldDenormalizer */
        $fieldDenormalizer = $this->getMockByCalls(FieldDenormalizerInterface::class);

        error_clear_last();

        $fieldMapping = DenormalizationFieldMappingBuilder::create('name', false, $fieldDenormalizer)->getMapping();

        $error = error_get_last();

        self::assertNotNull($error);

        self::assertSame(E_USER_DEPRECATED, $error['type']);
        self::assertSame(sprintf(
            '%s:create use %s:create',
            DenormalizationFieldMappingBuilder::class,
            DenormalizationFieldMappingFactory::class
        ), $error['message']);

        self::assertSame('name', $fieldMapping->getName());

        self::assertSame($fieldDenormalizer, $fieldMapping->getFieldDenormalizer());

        self::assertInstanceOf(NullPolicy::class, $fieldMapping->getPolicy());
    }

    public function testGetDefaultMapping(): void
    {
        $fieldMapping = DenormalizationFieldMappingBuilder::create('name')->getMapping();

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
        $fieldMapping = DenormalizationFieldMappingBuilder::create('name', true)->getMapping();

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
        error_clear_last();

        $fieldMapping = DenormalizationFieldMappingBuilder::createCallback('name', static function (): void {})->getMapping();

        $error = error_get_last();

        self::assertNotNull($error);

        self::assertSame(E_USER_DEPRECATED, $error['type']);
        self::assertSame(sprintf(
            '%s:createCallback use %s:createCallback',
            DenormalizationFieldMappingBuilder::class,
            DenormalizationFieldMappingFactory::class
        ), $error['message']);

        self::assertSame('name', $fieldMapping->getName());
        self::assertInstanceOf(CallbackFieldDenormalizer::class, $fieldMapping->getFieldDenormalizer());

        self::assertInstanceOf(NullPolicy::class, $fieldMapping->getPolicy());
    }

    public function testGetDefaultMappingForConvertType(): void
    {
        error_clear_last();

        $fieldMapping = DenormalizationFieldMappingBuilder::createConvertType(
            'name',
            ConvertTypeFieldDenormalizer::TYPE_FLOAT
        )->getMapping();

        $error = error_get_last();

        self::assertNotNull($error);

        self::assertSame(E_USER_DEPRECATED, $error['type']);
        self::assertSame(sprintf(
            '%s:createConvertType use %s:createConvertType',
            DenormalizationFieldMappingBuilder::class,
            DenormalizationFieldMappingFactory::class
        ), $error['message']);

        self::assertSame('name', $fieldMapping->getName());

        $fieldDenormalizer = $fieldMapping->getFieldDenormalizer();

        self::assertInstanceOf(ConvertTypeFieldDenormalizer::class, $fieldDenormalizer);

        $reflectionObject = new \ReflectionProperty($fieldDenormalizer, 'emptyToNull');
        $reflectionObject->setAccessible(true);

        self::assertFalse($reflectionObject->getValue($fieldDenormalizer));

        self::assertInstanceOf(NullPolicy::class, $fieldMapping->getPolicy());
    }

    public function testGetDefaultMappingForConvertTypeWithEmptyToNull(): void
    {
        $fieldMapping = DenormalizationFieldMappingBuilder::createConvertType(
            'name',
            ConvertTypeFieldDenormalizer::TYPE_FLOAT,
            true
        )->getMapping();

        self::assertSame('name', $fieldMapping->getName());

        $fieldDenormalizer = $fieldMapping->getFieldDenormalizer();

        self::assertInstanceOf(ConvertTypeFieldDenormalizer::class, $fieldDenormalizer);

        $reflectionObject = new \ReflectionProperty($fieldDenormalizer, 'emptyToNull');
        $reflectionObject->setAccessible(true);

        self::assertTrue($reflectionObject->getValue($fieldDenormalizer));

        self::assertInstanceOf(NullPolicy::class, $fieldMapping->getPolicy());
    }

    public function testGetDefaultMappingForDateTime(): void
    {
        error_clear_last();

        $fieldMapping = DenormalizationFieldMappingBuilder::createDateTime('name')->getMapping();

        $error = error_get_last();

        self::assertNotNull($error);

        self::assertSame(E_USER_DEPRECATED, $error['type']);
        self::assertSame(sprintf(
            '%s:createDateTime use %s:createDateTime',
            DenormalizationFieldMappingBuilder::class,
            DenormalizationFieldMappingFactory::class
        ), $error['message']);

        self::assertSame('name', $fieldMapping->getName());

        $fieldDenormalizer = $fieldMapping->getFieldDenormalizer();

        self::assertInstanceOf(DateTimeFieldDenormalizer::class, $fieldDenormalizer);

        $reflectionObject = new \ReflectionProperty($fieldDenormalizer, 'emptyToNull');
        $reflectionObject->setAccessible(true);

        self::assertFalse($reflectionObject->getValue($fieldDenormalizer));

        self::assertInstanceOf(NullPolicy::class, $fieldMapping->getPolicy());
    }

    public function testGetDefaultMappingForDateTimeWithEmptyToNull(): void
    {
        error_clear_last();

        $fieldMapping = DenormalizationFieldMappingBuilder::createDateTime('name', true)->getMapping();

        self::assertSame('name', $fieldMapping->getName());

        $fieldDenormalizer = $fieldMapping->getFieldDenormalizer();

        self::assertInstanceOf(DateTimeFieldDenormalizer::class, $fieldDenormalizer);

        $reflectionObject = new \ReflectionProperty($fieldDenormalizer, 'emptyToNull');
        $reflectionObject->setAccessible(true);

        self::assertTrue($reflectionObject->getValue($fieldDenormalizer));

        self::assertInstanceOf(NullPolicy::class, $fieldMapping->getPolicy());
    }

    public function testGetDefaultMappingForDateTimeWithTimezone(): void
    {
        $fieldMapping = DenormalizationFieldMappingBuilder::createDateTime('name', false, new \DateTimeZone('UTC'))->getMapping();

        self::assertSame('name', $fieldMapping->getName());
        self::assertInstanceOf(DateTimeFieldDenormalizer::class, $fieldMapping->getFieldDenormalizer());

        self::assertInstanceOf(NullPolicy::class, $fieldMapping->getPolicy());
    }

    public function testGetDefaultMappingForEmbedMany(): void
    {
        error_clear_last();

        $fieldMapping = DenormalizationFieldMappingBuilder::createEmbedMany('name', \stdClass::class)->getMapping();

        $error = error_get_last();

        self::assertNotNull($error);

        self::assertSame(E_USER_DEPRECATED, $error['type']);
        self::assertSame(sprintf(
            '%s:createEmbedMany use %s:createEmbedMany',
            DenormalizationFieldMappingBuilder::class,
            DenormalizationFieldMappingFactory::class
        ), $error['message']);

        self::assertSame('name', $fieldMapping->getName());
        self::assertInstanceOf(EmbedManyFieldDenormalizer::class, $fieldMapping->getFieldDenormalizer());

        self::assertInstanceOf(NullPolicy::class, $fieldMapping->getPolicy());
    }

    public function testGetDefaultMappingForEmbedOne(): void
    {
        error_clear_last();

        $fieldMapping = DenormalizationFieldMappingBuilder::createEmbedOne('name', \stdClass::class)->getMapping();

        $error = error_get_last();

        self::assertNotNull($error);

        self::assertSame(E_USER_DEPRECATED, $error['type']);
        self::assertSame(sprintf(
            '%s:createEmbedOne use %s:createEmbedOne',
            DenormalizationFieldMappingBuilder::class,
            DenormalizationFieldMappingFactory::class
        ), $error['message']);

        self::assertSame('name', $fieldMapping->getName());
        self::assertInstanceOf(EmbedOneFieldDenormalizer::class, $fieldMapping->getFieldDenormalizer());

        self::assertInstanceOf(NullPolicy::class, $fieldMapping->getPolicy());
    }

    public function testGetDefaultMappingForReferenceMany(): void
    {
        error_clear_last();

        $fieldMapping = DenormalizationFieldMappingBuilder::createReferenceMany('name', static function (): void {})->getMapping();

        $error = error_get_last();

        self::assertNotNull($error);

        self::assertSame(E_USER_DEPRECATED, $error['type']);
        self::assertSame(sprintf(
            '%s:createReferenceMany use %s:createReferenceMany',
            DenormalizationFieldMappingBuilder::class,
            DenormalizationFieldMappingFactory::class
        ), $error['message']);

        self::assertSame('name', $fieldMapping->getName());
        self::assertInstanceOf(ReferenceManyFieldDenormalizer::class, $fieldMapping->getFieldDenormalizer());

        self::assertInstanceOf(NullPolicy::class, $fieldMapping->getPolicy());
    }

    public function testGetDefaultMappingForReferenceOne(): void
    {
        error_clear_last();

        $fieldMapping = DenormalizationFieldMappingBuilder::createReferenceOne('name', static function (): void {})->getMapping();

        $error = error_get_last();

        self::assertNotNull($error);

        self::assertSame(E_USER_DEPRECATED, $error['type']);
        self::assertSame(sprintf(
            '%s:createReferenceOne use %s:createReferenceOne',
            DenormalizationFieldMappingBuilder::class,
            DenormalizationFieldMappingFactory::class
        ), $error['message']);

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
        error_clear_last();

        $fieldMapping = DenormalizationFieldMappingBuilder::createReferenceOne(
            'name',
            static function (): void {},
            true
        )->getMapping();

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
        /** @var FieldDenormalizerInterface|MockObject $fieldDenormalizer */
        $fieldDenormalizer = $this->getMockByCalls(FieldDenormalizerInterface::class);

        /** @var MockObject|PolicyInterface $policy */
        $policy = $this->getMockByCalls(PolicyInterface::class);

        $fieldMapping = DenormalizationFieldMappingBuilder::create('name', false, $fieldDenormalizer)
            ->setPolicy($policy)
            ->getMapping()
        ;

        self::assertSame('name', $fieldMapping->getName());
        self::assertSame($fieldDenormalizer, $fieldMapping->getFieldDenormalizer());

        self::assertSame($policy, $fieldMapping->getPolicy());
    }
}
