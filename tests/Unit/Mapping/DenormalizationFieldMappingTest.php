<?php

declare(strict_types=1);

namespace Chubbyphp\Tests\Deserialization\Unit\Mapping;

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
final class DenormalizationFieldMappingTest extends TestCase
{
    use MockByCallsTrait;

    public function testGetName(): void
    {
        /** @var FieldDenormalizerInterface|MockObject $fieldDenormalizer */
        $fieldDenormalizer = $this->getMockByCalls(FieldDenormalizerInterface::class);

        $fieldMapping = new DenormalizationFieldMapping('name', $fieldDenormalizer);

        self::assertSame('name', $fieldMapping->getName());
    }

    public function testGetFieldDenormalizer(): void
    {
        /** @var FieldDenormalizerInterface|MockObject $fieldDenormalizer */
        $fieldDenormalizer = $this->getMockByCalls(FieldDenormalizerInterface::class);

        $fieldMapping = new DenormalizationFieldMapping('name', $fieldDenormalizer);

        self::assertSame($fieldDenormalizer, $fieldMapping->getFieldDenormalizer());
    }

    public function testGetPolicy(): void
    {
        /** @var FieldDenormalizerInterface|MockObject $fieldDenormalizer */
        $fieldDenormalizer = $this->getMockByCalls(FieldDenormalizerInterface::class);

        /** @var MockObject|PolicyInterface $policy */
        $policy = $this->getMockByCalls(PolicyInterface::class);

        $fieldMapping = new DenormalizationFieldMapping('name', $fieldDenormalizer, $policy);

        self::assertSame($policy, $fieldMapping->getPolicy());
    }
}
