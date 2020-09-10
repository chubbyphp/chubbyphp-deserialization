<?php

declare(strict_types=1);

namespace Chubbyphp\Tests\Deserialization\Unit\ServiceFactory;

use Chubbyphp\Deserialization\Denormalizer\DenormalizerInterface;
use Chubbyphp\Deserialization\Denormalizer\DenormalizerObjectMappingRegistryInterface;
use Chubbyphp\Deserialization\ServiceFactory\DenormalizerFactory;
use Chubbyphp\Mock\Call;
use Chubbyphp\Mock\MockByCallsTrait;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;
use Psr\Log\LoggerInterface;

/**
 * @covers \Chubbyphp\Deserialization\ServiceFactory\DenormalizerFactory
 *
 * @internal
 */
final class DenormalizerFactoryTest extends TestCase
{
    use MockByCallsTrait;

    public function testInvoke(): void
    {
        /** @var DenormalizerObjectMappingRegistryInterface $normalizerObjectMappingRegistry */
        $normalizerObjectMappingRegistry = $this->getMockByCalls(DenormalizerObjectMappingRegistryInterface::class);

        /** @var ContainerInterface $container */
        $container = $this->getMockByCalls(ContainerInterface::class, [
            Call::create('has')->with(DenormalizerObjectMappingRegistryInterface::class)->willReturn(true),
            Call::create('get')
                ->with(DenormalizerObjectMappingRegistryInterface::class)
                ->willReturn($normalizerObjectMappingRegistry),
            Call::create('has')->with(LoggerInterface::class)->willReturn(false),
        ]);

        $factory = new DenormalizerFactory();

        $service = $factory($container);

        self::assertInstanceOf(DenormalizerInterface::class, $service);
    }

    public function testCallStatic(): void
    {
        /** @var DenormalizerObjectMappingRegistryInterface $normalizerObjectMappingRegistry */
        $normalizerObjectMappingRegistry = $this->getMockByCalls(DenormalizerObjectMappingRegistryInterface::class);

        /** @var ContainerInterface $container */
        $container = $this->getMockByCalls(ContainerInterface::class, [
            Call::create('has')->with(DenormalizerObjectMappingRegistryInterface::class.'default')->willReturn(true),
            Call::create('get')
                ->with(DenormalizerObjectMappingRegistryInterface::class.'default')
                ->willReturn($normalizerObjectMappingRegistry),
            Call::create('has')->with(LoggerInterface::class)->willReturn(false),
        ]);
        $factory = [DenormalizerFactory::class, 'default'];

        $service = $factory($container);

        self::assertInstanceOf(DenormalizerInterface::class, $service);
    }
}
