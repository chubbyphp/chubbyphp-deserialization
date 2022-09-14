<?php

declare(strict_types=1);

namespace Chubbyphp\Tests\Deserialization\Unit\ServiceFactory;

use Chubbyphp\DecodeEncode\Decoder\DecoderInterface;
use Chubbyphp\Deserialization\Decoder\DecoderInterface as OldDecoderInterface;
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
final class DeserializerFactoryTest extends TestCase
{
    use MockByCallsTrait;

    public function testInvokeWithDeprecatedOldDecoderInterface(): void
    {
        /** @var OldDecoderInterface $decoder */
        $decoder = $this->getMockByCalls(OldDecoderInterface::class);

        /** @var DenormalizerInterface $denormalizer */
        $denormalizer = $this->getMockByCalls(DenormalizerInterface::class);

        /** @var ContainerInterface $container */
        $container = $this->getMockByCalls(ContainerInterface::class, [
            Call::create('has')->with(OldDecoderInterface::class)->willReturn(true),
            Call::create('get')->with(OldDecoderInterface::class)->willReturn($decoder),
            Call::create('has')->with(DenormalizerInterface::class)->willReturn(true),
            Call::create('get')->with(DenormalizerInterface::class)->willReturn($denormalizer),
        ]);

        $factory = new DeserializerFactory();

        error_clear_last();

        $service = $factory($container);

        $error = error_get_last();

        self::assertNotNull($error);

        self::assertSame(E_USER_DEPRECATED, $error['type']);
        self::assertSame(sprintf(
            '%s use %s',
            OldDecoderInterface::class,
            DecoderInterface::class
        ), $error['message']);

        self::assertInstanceOf(DeserializerInterface::class, $service);
    }

    public function testCallStaticWithDeprecatedOldDecoderInterface(): void
    {
        /** @var OldDecoderInterface $decoder */
        $decoder = $this->getMockByCalls(OldDecoderInterface::class);

        /** @var DenormalizerInterface $denormalizer */
        $denormalizer = $this->getMockByCalls(DenormalizerInterface::class);

        /** @var ContainerInterface $container */
        $container = $this->getMockByCalls(ContainerInterface::class, [
            Call::create('has')->with(OldDecoderInterface::class.'default')->willReturn(true),
            Call::create('get')->with(OldDecoderInterface::class.'default')->willReturn($decoder),
            Call::create('has')->with(DenormalizerInterface::class.'default')->willReturn(true),
            Call::create('get')->with(DenormalizerInterface::class.'default')->willReturn($denormalizer),
        ]);

        $factory = [DeserializerFactory::class, 'default'];

        error_clear_last();

        $service = $factory($container);

        $error = error_get_last();

        self::assertNotNull($error);

        self::assertSame(E_USER_DEPRECATED, $error['type']);
        self::assertSame(sprintf(
            '%s use %s',
            OldDecoderInterface::class,
            DecoderInterface::class
        ), $error['message']);

        self::assertInstanceOf(DeserializerInterface::class, $service);
    }

    public function testInvoke(): void
    {
        /** @var DecoderInterface $decoder */
        $decoder = $this->getMockByCalls(DecoderInterface::class);

        /** @var DenormalizerInterface $denormalizer */
        $denormalizer = $this->getMockByCalls(DenormalizerInterface::class);

        /** @var ContainerInterface $container */
        $container = $this->getMockByCalls(ContainerInterface::class, [
            Call::create('has')->with(OldDecoderInterface::class)->willReturn(false),
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
            Call::create('has')->with(OldDecoderInterface::class.'default')->willReturn(false),
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
