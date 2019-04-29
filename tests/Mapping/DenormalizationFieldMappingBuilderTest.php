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
use Chubbyphp\Mock\MockByCallsTrait;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Chubbyphp\Deserialization\Mapping\DenormalizationFieldMappingBuilder
 */
class DenormalizationFieldMappingBuilderTest extends TestCase
{
    use MockByCallsTrait;

    public function testGetDefaultMapping()
    {
        $fieldMapping = DenormalizationFieldMappingBuilder::create('name')->getMapping();

        self::assertSame('name', $fieldMapping->getName());
        self::assertSame([], $fieldMapping->getGroups());
        self::assertInstanceOf(FieldDenormalizer::class, $fieldMapping->getFieldDenormalizer());
    }

    public function testGetDefaultMappingForCallback()
    {
        $fieldMapping = DenormalizationFieldMappingBuilder::createCallback('name', function () {})->getMapping();

        self::assertSame('name', $fieldMapping->getName());
        self::assertSame([], $fieldMapping->getGroups());
        self::assertInstanceOf(CallbackFieldDenormalizer::class, $fieldMapping->getFieldDenormalizer());
    }

    public function testGetDefaultMappingForConvertType()
    {
        $fieldMapping = DenormalizationFieldMappingBuilder::createConvertType(
            'name',
            ConvertTypeFieldDenormalizer::TYPE_FLOAT
        )->getMapping();

        self::assertSame('name', $fieldMapping->getName());
        self::assertSame([], $fieldMapping->getGroups());
        self::assertInstanceOf(ConvertTypeFieldDenormalizer::class, $fieldMapping->getFieldDenormalizer());
    }

    public function testGetDefaultMappingForDateTime()
    {
        $fieldMapping = DenormalizationFieldMappingBuilder::createDateTime('name')->getMapping();

        self::assertSame('name', $fieldMapping->getName());
        self::assertSame([], $fieldMapping->getGroups());
        self::assertInstanceOf(DateTimeFieldDenormalizer::class, $fieldMapping->getFieldDenormalizer());
    }

    public function testGetDefaultMappingForDateTimeWithTimezone()
    {
        $fieldMapping = DenormalizationFieldMappingBuilder::createDateTime('name', false, new \DateTimeZone('UTC'))->getMapping();

        self::assertSame('name', $fieldMapping->getName());
        self::assertSame([], $fieldMapping->getGroups());
        self::assertInstanceOf(DateTimeFieldDenormalizer::class, $fieldMapping->getFieldDenormalizer());
    }

    public function testGetDefaultMappingForEmbedMany()
    {
        $fieldMapping = DenormalizationFieldMappingBuilder::createEmbedMany('name', \stdClass::class)->getMapping();

        self::assertSame('name', $fieldMapping->getName());
        self::assertSame([], $fieldMapping->getGroups());
        self::assertInstanceOf(EmbedManyFieldDenormalizer::class, $fieldMapping->getFieldDenormalizer());
    }

    public function testGetDefaultMappingForEmbedOne()
    {
        $fieldMapping = DenormalizationFieldMappingBuilder::createEmbedOne('name', \stdClass::class)->getMapping();

        self::assertSame('name', $fieldMapping->getName());
        self::assertSame([], $fieldMapping->getGroups());
        self::assertInstanceOf(EmbedOneFieldDenormalizer::class, $fieldMapping->getFieldDenormalizer());
    }

    public function testGetDefaultMappingForReferenceMany()
    {
        $fieldMapping = DenormalizationFieldMappingBuilder::createReferenceMany('name', function () {})->getMapping();

        self::assertSame('name', $fieldMapping->getName());
        self::assertSame([], $fieldMapping->getGroups());
        self::assertInstanceOf(ReferenceManyFieldDenormalizer::class, $fieldMapping->getFieldDenormalizer());
    }

    public function testGetDefaultMappingForReferenceOne()
    {
        $fieldMapping = DenormalizationFieldMappingBuilder::createReferenceOne('name', function () {})->getMapping();

        self::assertSame('name', $fieldMapping->getName());
        self::assertSame([], $fieldMapping->getGroups());
        self::assertInstanceOf(ReferenceOneFieldDenormalizer::class, $fieldMapping->getFieldDenormalizer());
    }

    public function testGetMapping()
    {
        /** @var FieldDenormalizerInterface|MockObject $fieldDenormalizer */
        $fieldDenormalizer = $this->getMockByCalls(FieldDenormalizerInterface::class);

        $fieldMapping = DenormalizationFieldMappingBuilder::create('name')
            ->setGroups(['group1'])
            ->setFieldDenormalizer($fieldDenormalizer)
            ->getMapping();

        self::assertSame('name', $fieldMapping->getName());
        self::assertSame(['group1'], $fieldMapping->getGroups());
        self::assertSame($fieldDenormalizer, $fieldMapping->getFieldDenormalizer());
    }
}
