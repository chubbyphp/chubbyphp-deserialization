<?php

declare(strict_types=1);

namespace Chubbyphp\Tests\Deserialization\Unit\ServiceFactory;

use Chubbyphp\Deserialization\Denormalizer\DenormalizerObjectMappingRegistryInterface;
use Chubbyphp\Deserialization\Mapping\DenormalizationObjectMappingInterface;
use Chubbyphp\Deserialization\ServiceFactory\DenormalizerObjectMappingRegistryFactory;
use Chubbyphp\Mock\Call;
use Chubbyphp\Mock\MockByCallsTrait;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;

/**
 * @covers \Chubbyphp\Deserialization\ServiceFactory\DenormalizerObjectMappingRegistryFactory
 *
 * @internal
 */
final class DenormalizerObjectMappingRegistryFactoryTest extends TestCase
{
    use MockByCallsTrait;

    public function testInvoke(): void
    {
        /** @var ContainerInterface $container */
        $container = $this->getMockByCalls(ContainerInterface::class, [
            Call::create('get')->with(DenormalizationObjectMappingInterface::class.'[]')->willReturn([]),
        ]);

        $factory = new DenormalizerObjectMappingRegistryFactory();

        $service = $factory($container);

        self::assertInstanceOf(DenormalizerObjectMappingRegistryInterface::class, $service);
    }

    public function testCallStatic(): void
    {
        /** @var ContainerInterface $container */
        $container = $this->getMockByCalls(ContainerInterface::class, [
            Call::create('get')->with(DenormalizationObjectMappingInterface::class.'[]default')->willReturn([]),
        ]);

        $factory = [DenormalizerObjectMappingRegistryFactory::class, 'default'];

        $service = $factory($container);

        self::assertInstanceOf(DenormalizerObjectMappingRegistryInterface::class, $service);
    }
}
