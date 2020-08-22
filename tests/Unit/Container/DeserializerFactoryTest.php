<?php

declare(strict_types=1);

namespace Chubbyphp\Tests\Deserialization\Unit\Container;

use Chubbyphp\Deserialization\Container\DeserializerFactory;
use Chubbyphp\Deserialization\Decoder\DecoderInterface;
use Chubbyphp\Deserialization\Denormalizer\DenormalizerInterface;
use Chubbyphp\Deserialization\DeserializerInterface;
use Chubbyphp\Mock\Call;
use Chubbyphp\Mock\MockByCallsTrait;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;

/**
 * @covers \Chubbyphp\Deserialization\Container\DeserializerFactory
 *
 * @internal
 */
final class DeserializerFactoryTest extends TestCase
{
    use MockByCallsTrait;

    public function testInvoke(): void
    {
        /** @var DenormalizerInterface $normalizer */
        $normalizer = $this->getMockByCalls(DenormalizerInterface::class);

        /** @var DecoderInterface $encoder */
        $encoder = $this->getMockByCalls(DecoderInterface::class);

        /** @var ContainerInterface $container */
        $container = $this->getMockByCalls(ContainerInterface::class, [
            Call::create('get')->with(DecoderInterface::class)->willReturn($encoder),
            Call::create('get')->with(DenormalizerInterface::class)->willReturn($normalizer),
        ]);

        $factory = new DeserializerFactory();

        $negotiator = $factory($container);

        self::assertInstanceOf(DeserializerInterface::class, $negotiator);
    }
}
