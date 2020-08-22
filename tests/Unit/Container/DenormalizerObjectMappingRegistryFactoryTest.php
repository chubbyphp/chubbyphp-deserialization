<?php

declare(strict_types=1);

namespace Chubbyphp\Tests\Deserialization\Unit\Container;

use Chubbyphp\Deserialization\Container\DenormalizerObjectMappingRegistryFactory;
use Chubbyphp\Deserialization\Denormalizer\DenormalizerObjectMappingRegistryInterface;
use Chubbyphp\Deserialization\Mapping\DenormalizationObjectMappingInterface;
use Chubbyphp\Mock\Call;
use Chubbyphp\Mock\MockByCallsTrait;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;

/**
 * @covers \Chubbyphp\Deserialization\Container\DenormalizerObjectMappingRegistryFactory
 *
 * @internal
 */
final class DenormalizerObjectMappingRegistryFactoryTest extends TestCase
{
    use MockByCallsTrait;

    public function testInvoke(): void
    {
        /** @var DenormalizationObjectMappingInterface $normalizationObjectMapping */
        $normalizationObjectMapping = $this->getMockByCalls(DenormalizationObjectMappingInterface::class, [
            Call::create('getClass')->with()->willReturn('class'),
        ]);

        /** @var ContainerInterface $container */
        $container = $this->getMockByCalls(ContainerInterface::class, [
            Call::create('get')
                ->with(DenormalizationObjectMappingInterface::class.'[]')
                ->willReturn([$normalizationObjectMapping]),
        ]);

        $factory = new DenormalizerObjectMappingRegistryFactory();

        $negotiator = $factory($container);

        self::assertInstanceOf(DenormalizerObjectMappingRegistryInterface::class, $negotiator);
    }
}
