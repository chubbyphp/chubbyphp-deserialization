<?php

declare(strict_types=1);

namespace Chubbyphp\Tests\Deserialization\Mapping;

use Chubbyphp\Deserialization\Denormalizer\FieldDenormalizerInterface;
use Chubbyphp\Deserialization\Mapping\DenormalizationFieldMapping;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Chubbyphp\Deserialization\Mapping\DenormalizationFieldMapping
 */
class DenormalizationFieldMappingTest extends TestCase
{
    public function testGetName()
    {
        $fieldMapping = new DenormalizationFieldMapping('name', ['group1'], $this->getFieldDenormalizer());

        self::assertSame('name', $fieldMapping->getName());
    }

    public function testGetGroups()
    {
        $fieldMapping = new DenormalizationFieldMapping('name', ['group1'], $this->getFieldDenormalizer());

        self::assertSame(['group1'], $fieldMapping->getGroups());
    }

    public function testGetFieldDenormalizer()
    {
        $fieldDenormalizer = $this->getFieldDenormalizer();

        $fieldMapping = new DenormalizationFieldMapping('name', ['group1'], $fieldDenormalizer);

        self::assertSame($fieldDenormalizer, $fieldMapping->getFieldDenormalizer());
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
