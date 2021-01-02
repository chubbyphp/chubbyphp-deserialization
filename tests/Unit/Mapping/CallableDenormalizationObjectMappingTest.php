<?php

declare(strict_types=1);

namespace Chubbyphp\Tests\Deserialization\Unit\Mapping;

use Chubbyphp\Deserialization\Mapping\CallableDenormalizationObjectMapping;
use Chubbyphp\Deserialization\Mapping\DenormalizationFieldMappingInterface;
use Chubbyphp\Deserialization\Mapping\DenormalizationObjectMappingInterface;
use Chubbyphp\Mock\Call;
use Chubbyphp\Mock\MockByCallsTrait;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Chubbyphp\Deserialization\Mapping\CallableDenormalizationObjectMapping
 *
 * @internal
 */
final class CallableDenormalizationObjectMappingTest extends TestCase
{
    use MockByCallsTrait;

    public function testGetClass(): void
    {
        $mapping = new CallableDenormalizationObjectMapping(\stdClass::class, function (): void {});

        self::assertSame(\stdClass::class, $mapping->getClass());
    }

    public function testGetDenormalizationFactory(): void
    {
        $object = new \stdClass();

        $mapping = new CallableDenormalizationObjectMapping(\stdClass::class, fn () => $this->getMockByCalls(DenormalizationObjectMappingInterface::class, [
            Call::create('getDenormalizationFactory')->with('path', null)->willReturn(fn () => $object),
        ]));

        $factory = $mapping->getDenormalizationFactory('path');

        self::assertInstanceOf(\Closure::class, $factory);

        self::assertSame($object, $factory());
    }

    public function testGetDenormalizationFieldMappings(): void
    {
        $fieldMapping = $this->getMockByCalls(DenormalizationFieldMappingInterface::class);

        $mapping = new CallableDenormalizationObjectMapping(\stdClass::class, fn () => $this->getMockByCalls(DenormalizationObjectMappingInterface::class, [
            Call::create('getDenormalizationFieldMappings')->with('path', null)->willReturn([$fieldMapping]),
        ]));

        self::assertSame($fieldMapping, $mapping->getDenormalizationFieldMappings('path')[0]);
    }
}
