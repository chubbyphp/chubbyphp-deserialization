<?php

declare(strict_types=1);

namespace Chubbyphp\Tests\Deserialization\Mapping;

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
class DenormalizationFieldMappingBuilderTest extends TestCase
{
    use MockByCallsTrait;

    public function testGetMappingWithDenormalizer()
    {
        /** @var FieldDenormalizerInterface|MockObject $fieldDenormalizer */
        $fieldDenormalizer = $this->getMockByCalls(FieldDenormalizerInterface::class);

        $fieldMapping = DenormalizationFieldMappingBuilder::create('name', false, $fieldDenormalizer)->getMapping();

        self::assertSame('name', $fieldMapping->getName());
        self::assertSame([], $fieldMapping->getGroups());

        self::assertSame($fieldDenormalizer, $fieldMapping->getFieldDenormalizer());

        self::assertInstanceOf(NullPolicy::class, $fieldMapping->getPolicy());
    }

    public function testGetDefaultMapping()
    {
        $fieldMapping = DenormalizationFieldMappingBuilder::create('name')->getMapping();

        self::assertSame('name', $fieldMapping->getName());
        self::assertSame([], $fieldMapping->getGroups());

        $fieldDenormalizer = $fieldMapping->getFieldDenormalizer();

        self::assertInstanceOf(FieldDenormalizer::class, $fieldDenormalizer);

        $reflectionObject = new \ReflectionProperty($fieldDenormalizer, 'emptyToNull');
        $reflectionObject->setAccessible(true);

        self::assertFalse($reflectionObject->getValue($fieldDenormalizer));

        self::assertInstanceOf(NullPolicy::class, $fieldMapping->getPolicy());
    }

    public function testGetDefaultMappingWithEmptyToNull()
    {
        $fieldMapping = DenormalizationFieldMappingBuilder::create('name', true)->getMapping();

        self::assertSame('name', $fieldMapping->getName());
        self::assertSame([], $fieldMapping->getGroups());

        $fieldDenormalizer = $fieldMapping->getFieldDenormalizer();

        self::assertInstanceOf(FieldDenormalizer::class, $fieldDenormalizer);

        $reflectionObject = new \ReflectionProperty($fieldDenormalizer, 'emptyToNull');
        $reflectionObject->setAccessible(true);

        self::assertTrue($reflectionObject->getValue($fieldDenormalizer));

        self::assertInstanceOf(NullPolicy::class, $fieldMapping->getPolicy());
    }

    public function testGetDefaultMappingForCallback()
    {
        $fieldMapping = DenormalizationFieldMappingBuilder::createCallback('name', function () {})->getMapping();

        self::assertSame('name', $fieldMapping->getName());
        self::assertSame([], $fieldMapping->getGroups());
        self::assertInstanceOf(CallbackFieldDenormalizer::class, $fieldMapping->getFieldDenormalizer());

        self::assertInstanceOf(NullPolicy::class, $fieldMapping->getPolicy());
    }

    public function testGetDefaultMappingForConvertType()
    {
        $fieldMapping = DenormalizationFieldMappingBuilder::createConvertType(
            'name',
            ConvertTypeFieldDenormalizer::TYPE_FLOAT
        )->getMapping();

        self::assertSame('name', $fieldMapping->getName());
        self::assertSame([], $fieldMapping->getGroups());

        $fieldDenormalizer = $fieldMapping->getFieldDenormalizer();

        self::assertInstanceOf(ConvertTypeFieldDenormalizer::class, $fieldDenormalizer);

        $reflectionObject = new \ReflectionProperty($fieldDenormalizer, 'emptyToNull');
        $reflectionObject->setAccessible(true);

        self::assertFalse($reflectionObject->getValue($fieldDenormalizer));

        self::assertInstanceOf(NullPolicy::class, $fieldMapping->getPolicy());
    }

    public function testGetDefaultMappingForConvertTypeWithEmptyToNull()
    {
        $fieldMapping = DenormalizationFieldMappingBuilder::createConvertType(
            'name',
            ConvertTypeFieldDenormalizer::TYPE_FLOAT,
            true
        )->getMapping();

        self::assertSame('name', $fieldMapping->getName());
        self::assertSame([], $fieldMapping->getGroups());

        $fieldDenormalizer = $fieldMapping->getFieldDenormalizer();

        self::assertInstanceOf(ConvertTypeFieldDenormalizer::class, $fieldDenormalizer);

        $reflectionObject = new \ReflectionProperty($fieldDenormalizer, 'emptyToNull');
        $reflectionObject->setAccessible(true);

        self::assertTrue($reflectionObject->getValue($fieldDenormalizer));

        self::assertInstanceOf(NullPolicy::class, $fieldMapping->getPolicy());
    }

    public function testGetDefaultMappingForDateTime()
    {
        $fieldMapping = DenormalizationFieldMappingBuilder::createDateTime('name')->getMapping();

        self::assertSame('name', $fieldMapping->getName());
        self::assertSame([], $fieldMapping->getGroups());

        $fieldDenormalizer = $fieldMapping->getFieldDenormalizer();

        self::assertInstanceOf(DateTimeFieldDenormalizer::class, $fieldDenormalizer);

        $reflectionObject = new \ReflectionProperty($fieldDenormalizer, 'emptyToNull');
        $reflectionObject->setAccessible(true);

        self::assertFalse($reflectionObject->getValue($fieldDenormalizer));

        self::assertInstanceOf(NullPolicy::class, $fieldMapping->getPolicy());
    }

    public function testGetDefaultMappingForDateTimeWithEmptyToNull()
    {
        $fieldMapping = DenormalizationFieldMappingBuilder::createDateTime('name', true)->getMapping();

        self::assertSame('name', $fieldMapping->getName());
        self::assertSame([], $fieldMapping->getGroups());

        $fieldDenormalizer = $fieldMapping->getFieldDenormalizer();

        self::assertInstanceOf(DateTimeFieldDenormalizer::class, $fieldDenormalizer);

        $reflectionObject = new \ReflectionProperty($fieldDenormalizer, 'emptyToNull');
        $reflectionObject->setAccessible(true);

        self::assertTrue($reflectionObject->getValue($fieldDenormalizer));

        self::assertInstanceOf(NullPolicy::class, $fieldMapping->getPolicy());
    }

    public function testGetDefaultMappingForDateTimeWithTimezone()
    {
        $fieldMapping = DenormalizationFieldMappingBuilder::createDateTime('name', false, new \DateTimeZone('UTC'))->getMapping();

        self::assertSame('name', $fieldMapping->getName());
        self::assertSame([], $fieldMapping->getGroups());
        self::assertInstanceOf(DateTimeFieldDenormalizer::class, $fieldMapping->getFieldDenormalizer());

        self::assertInstanceOf(NullPolicy::class, $fieldMapping->getPolicy());
    }

    public function testGetDefaultMappingForEmbedMany()
    {
        $fieldMapping = DenormalizationFieldMappingBuilder::createEmbedMany('name', \stdClass::class)->getMapping();

        self::assertSame('name', $fieldMapping->getName());
        self::assertSame([], $fieldMapping->getGroups());
        self::assertInstanceOf(EmbedManyFieldDenormalizer::class, $fieldMapping->getFieldDenormalizer());

        self::assertInstanceOf(NullPolicy::class, $fieldMapping->getPolicy());
    }

    public function testGetDefaultMappingForEmbedOne()
    {
        $fieldMapping = DenormalizationFieldMappingBuilder::createEmbedOne('name', \stdClass::class)->getMapping();

        self::assertSame('name', $fieldMapping->getName());
        self::assertSame([], $fieldMapping->getGroups());
        self::assertInstanceOf(EmbedOneFieldDenormalizer::class, $fieldMapping->getFieldDenormalizer());

        self::assertInstanceOf(NullPolicy::class, $fieldMapping->getPolicy());
    }

    public function testGetDefaultMappingForReferenceMany()
    {
        $fieldMapping = DenormalizationFieldMappingBuilder::createReferenceMany('name', function () {})->getMapping();

        self::assertSame('name', $fieldMapping->getName());
        self::assertSame([], $fieldMapping->getGroups());
        self::assertInstanceOf(ReferenceManyFieldDenormalizer::class, $fieldMapping->getFieldDenormalizer());

        self::assertInstanceOf(NullPolicy::class, $fieldMapping->getPolicy());
    }

    public function testGetDefaultMappingForReferenceOne()
    {
        $fieldMapping = DenormalizationFieldMappingBuilder::createReferenceOne('name', function () {})->getMapping();

        self::assertSame('name', $fieldMapping->getName());
        self::assertSame([], $fieldMapping->getGroups());

        $fieldDenormalizer = $fieldMapping->getFieldDenormalizer();

        self::assertInstanceOf(ReferenceOneFieldDenormalizer::class, $fieldDenormalizer);

        $reflectionObject = new \ReflectionProperty($fieldDenormalizer, 'emptyToNull');
        $reflectionObject->setAccessible(true);

        self::assertFalse($reflectionObject->getValue($fieldDenormalizer));

        self::assertInstanceOf(NullPolicy::class, $fieldMapping->getPolicy());
    }

    public function testGetDefaultMappingForReferenceOneWithEmptyToNull()
    {
        $fieldMapping = DenormalizationFieldMappingBuilder::createReferenceOne(
            'name',
            function () {},
            true
        )->getMapping();

        self::assertSame('name', $fieldMapping->getName());
        self::assertSame([], $fieldMapping->getGroups());

        $fieldDenormalizer = $fieldMapping->getFieldDenormalizer();

        self::assertInstanceOf(ReferenceOneFieldDenormalizer::class, $fieldDenormalizer);

        $reflectionObject = new \ReflectionProperty($fieldDenormalizer, 'emptyToNull');
        $reflectionObject->setAccessible(true);

        self::assertTrue($reflectionObject->getValue($fieldDenormalizer));

        self::assertInstanceOf(NullPolicy::class, $fieldMapping->getPolicy());
    }

    public function testGetMapping()
    {
        /** @var FieldDenormalizerInterface|MockObject $fieldDenormalizer */
        $fieldDenormalizer = $this->getMockByCalls(FieldDenormalizerInterface::class);

        /** @var PolicyInterface|MockObject $policy */
        $policy = $this->getMockByCalls(PolicyInterface::class);

        error_clear_last();

        $fieldMapping = DenormalizationFieldMappingBuilder::create('name')
            ->setGroups(['group1'])
            ->setFieldDenormalizer($fieldDenormalizer)
            ->setPolicy($policy)
            ->getMapping()
        ;

        $error = error_get_last();

        self::assertSame(E_USER_DEPRECATED, $error['type']);
        self::assertSame('Utilize third parameter of create method instead', $error['message']);

        self::assertSame('name', $fieldMapping->getName());
        self::assertSame(['group1'], $fieldMapping->getGroups());
        self::assertSame($fieldDenormalizer, $fieldMapping->getFieldDenormalizer());

        self::assertSame($policy, $fieldMapping->getPolicy());
    }
}
