<?php

declare(strict_types=1);

namespace Chubbyphp\Tests\Deserialization\Mapping;

use Chubbyphp\Deserialization\Denormalizer\FieldDenormalizerInterface;
use Chubbyphp\Deserialization\Mapping\DenormalizationFieldMapping;
use Chubbyphp\Deserialization\Policy\PolicyInterface;
use Chubbyphp\Mock\MockByCallsTrait;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Chubbyphp\Deserialization\Mapping\DenormalizationFieldMapping
 *
 * @internal
 */
class DenormalizationFieldMappingTest extends TestCase
{
    use MockByCallsTrait;

    public function testGetName()
    {
        /** @var FieldDenormalizerInterface|MockObject $fieldDenormalizer */
        $fieldDenormalizer = $this->getMockByCalls(FieldDenormalizerInterface::class);

        $fieldMapping = new DenormalizationFieldMapping('name', ['group1'], $fieldDenormalizer);

        self::assertSame('name', $fieldMapping->getName());
    }

    public function testGetGroups()
    {
        /** @var FieldDenormalizerInterface|MockObject $fieldDenormalizer */
        $fieldDenormalizer = $this->getMockByCalls(FieldDenormalizerInterface::class);

        $fieldMapping = new DenormalizationFieldMapping('name', ['group1'], $fieldDenormalizer);

        self::assertSame(['group1'], $fieldMapping->getGroups());
    }

    public function testGetFieldDenormalizer()
    {
        /** @var FieldDenormalizerInterface|MockObject $fieldDenormalizer */
        $fieldDenormalizer = $this->getMockByCalls(FieldDenormalizerInterface::class);

        $fieldMapping = new DenormalizationFieldMapping('name', ['group1'], $fieldDenormalizer);

        self::assertSame($fieldDenormalizer, $fieldMapping->getFieldDenormalizer());
    }

    public function testGetPolicy()
    {
        /** @var FieldDenormalizerInterface|MockObject $fieldDenormalizer */
        $fieldDenormalizer = $this->getMockByCalls(FieldDenormalizerInterface::class);

        /** @var PolicyInterface|MockObject $policy */
        $policy = $this->getMockByCalls(PolicyInterface::class);

        $fieldMapping = new DenormalizationFieldMapping('name', ['group1'], $fieldDenormalizer, $policy);

        self::assertSame($policy, $fieldMapping->getPolicy());
    }
}
