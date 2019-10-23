<?php

declare(strict_types=1);

namespace Chubbyphp\Tests\Deserialization\Unit\Mapping;

use Chubbyphp\Deserialization\Mapping\DenormalizationFieldMappingInterface;
use Chubbyphp\Deserialization\Mapping\DenormalizationObjectMappingInterface;
use Chubbyphp\Deserialization\Mapping\LazyDenormalizationObjectMapping;
use Chubbyphp\Mock\Call;
use Chubbyphp\Mock\MockByCallsTrait;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;

/**
 * @covers \Chubbyphp\Deserialization\Mapping\LazyDenormalizationObjectMapping
 *
 * @internal
 */
final class LazyDenormalizationObjectMappingTest extends TestCase
{
    use MockByCallsTrait;

    public function testInvoke(): void
    {
        $denormalizationFieldMappings = [$this->getMockByCalls(DenormalizationFieldMappingInterface::class)];

        $factory = function (): void {
        };

        /** @var DenormalizationObjectMappingInterface|MockObject $denormalizationObjectMapping */
        $denormalizationObjectMapping = $this->getMockByCalls(DenormalizationObjectMappingInterface::class, [
            Call::create('getDenormalizationFactory')->with('path', 'type')->willReturn($factory),
            Call::create('getDenormalizationFieldMappings')
                ->with('path', 'type')
                ->willReturn($denormalizationFieldMappings),
        ]);

        /** @var ContainerInterface|MockObject $container */
        $container = $this->getMockByCalls(ContainerInterface::class, [
            Call::create('get')->with('service')->willReturn($denormalizationObjectMapping),
            Call::create('get')->with('service')->willReturn($denormalizationObjectMapping),
        ]);

        $objectMapping = new LazyDenormalizationObjectMapping($container, 'service', \stdClass::class);

        self::assertEquals(\stdClass::class, $objectMapping->getClass());
        self::assertSame($factory, $objectMapping->getDenormalizationFactory('path', 'type'));
        self::assertSame($denormalizationFieldMappings, $objectMapping->getDenormalizationFieldMappings('path', 'type'));
    }
}
