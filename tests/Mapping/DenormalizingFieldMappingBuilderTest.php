<?php

declare(strict_types=1);

namespace Chubbyphp\Tests\Deserialization\Mapping;

use Chubbyphp\Deserialization\Denormalizer\FieldDenormalizerInterface;
use Chubbyphp\Deserialization\Mapping\DenormalizingFieldMappingBuilder;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Chubbyphp\Deserialization\Mapping\DenormalizingFieldMappingBuilder
 */
class DenormalizingFieldMappingBuilderTest extends TestCase
{
    public function testGetDefaultMapping()
    {
        $fieldMapping = DenormalizingFieldMappingBuilder::create('name')->getMapping();

        self::assertSame('name', $fieldMapping->getName());
        self::assertSame([], $fieldMapping->getGroups());
        self::assertInstanceOf(FieldDenormalizerInterface::class, $fieldMapping->getFieldDenormalizer());
    }

    public function testGetMapping()
    {
        $denormalizer = $this->getFieldDenormalizer();

        $fieldMapping = DenormalizingFieldMappingBuilder::create('name')
            ->setGroups(['group1'])
            ->setFieldDenormalizer($denormalizer)
            ->getMapping();

        self::assertSame('name', $fieldMapping->getName());
        self::assertSame(['group1'], $fieldMapping->getGroups());
        self::assertSame($denormalizer, $fieldMapping->getFieldDenormalizer());
    }

    /**
     * @return FieldDenormalizerInterface
     */
    private function getFieldDenormalizer(): FieldDenormalizerInterface
    {
        /** @var FieldDenormalizerInterface|\PHPUnit_Framework_MockObject_MockObject $fieldDenormalizer */
        $fieldDenormalizer = $this->getMockBuilder(FieldDenormalizerInterface::class)->getMockForAbstractClass();

        return $fieldDenormalizer;
    }
}
