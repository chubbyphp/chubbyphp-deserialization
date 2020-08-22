<?php

declare(strict_types=1);

namespace Chubbyphp\Tests\Deserialization\Unit\Container;

use Chubbyphp\Deserialization\Container\DenormalizerFactory;
use Chubbyphp\Deserialization\Denormalizer\DenormalizerInterface;
use Chubbyphp\Deserialization\Denormalizer\DenormalizerObjectMappingRegistryInterface;
use Chubbyphp\Mock\Call;
use Chubbyphp\Mock\MockByCallsTrait;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;
use Psr\Log\LoggerInterface;

/**
 * @covers \Chubbyphp\Deserialization\Container\DenormalizerFactory
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

        /** @var LoggerInterface $logger */
        $logger = $this->getMockByCalls(LoggerInterface::class);

        /** @var ContainerInterface $container */
        $container = $this->getMockByCalls(ContainerInterface::class, [
            Call::create('get')
                ->with(DenormalizerObjectMappingRegistryInterface::class)
                ->willReturn($normalizerObjectMappingRegistry),
            Call::create('get')->with(LoggerInterface::class)->willReturn($logger),
        ]);

        $factory = new DenormalizerFactory();

        $negotiator = $factory($container);

        self::assertInstanceOf(DenormalizerInterface::class, $negotiator);
    }
}
