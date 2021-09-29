<?php

declare(strict_types=1);

namespace Chubbyphp\Tests\Deserialization\Unit\ServiceFactory;

use Chubbyphp\Deserialization\Decoder\DecoderInterface;
use Chubbyphp\Deserialization\Denormalizer\DenormalizerInterface;
use Chubbyphp\Deserialization\DeserializerInterface;
use Chubbyphp\Deserialization\ServiceFactory\DeserializerFactory;
use Chubbyphp\Mock\Call;
use Chubbyphp\Mock\MockByCallsTrait;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;

/**
 * @covers \Chubbyphp\Deserialization\ServiceFactory\DeserializerFactory
 *
 * @internal
 */
final class SerializerFactoryTest extends TestCase
{
    use MockByCallsTrait;

    public function testInvoke(): void
    {
        /** @var DecoderInterface $decoder */
        $decoder = $this->getMockByCalls(DecoderInterface::class);

        /** @var DenormalizerInterface $denormalizer */
        $denormalizer = $this->getMockByCalls(DenormalizerInterface::class);

        /** @var ContainerInterface $container */
        $container = $this->getMockByCalls(ContainerInterface::class, [
            Call::create('has')->with(DecoderInterface::class)->willReturn(true),
            Call::create('get')->with(DecoderInterface::class)->willReturn($decoder),
            Call::create('has')->with(DenormalizerInterface::class)->willReturn(true),
            Call::create('get')->with(DenormalizerInterface::class)->willReturn($denormalizer),
        ]);

        $factory = new DeserializerFactory();

        $service = $factory($container);

        self::assertInstanceOf(DeserializerInterface::class, $service);
    }

    public function testCallStatic(): void
    {
        /** @var DecoderInterface $decoder */
        $decoder = $this->getMockByCalls(DecoderInterface::class);

        /** @var DenormalizerInterface $denormalizer */
        $denormalizer = $this->getMockByCalls(DenormalizerInterface::class);

        /** @var ContainerInterface $container */
        $container = $this->getMockByCalls(ContainerInterface::class, [
            Call::create('has')->with(DecoderInterface::class.'default')->willReturn(true),
            Call::create('get')->with(DecoderInterface::class.'default')->willReturn($decoder),
            Call::create('has')->with(DenormalizerInterface::class.'default')->willReturn(true),
            Call::create('get')->with(DenormalizerInterface::class.'default')->willReturn($denormalizer),
        ]);

        $factory = [DeserializerFactory::class, 'default'];

        $service = $factory($container);

        self::assertInstanceOf(DeserializerInterface::class, $service);
    }
}
