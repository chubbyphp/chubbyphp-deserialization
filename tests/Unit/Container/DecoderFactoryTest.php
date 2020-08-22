<?php

declare(strict_types=1);

namespace Chubbyphp\Tests\Deserialization\Unit\Container;

use Chubbyphp\Deserialization\Container\DecoderFactory;
use Chubbyphp\Deserialization\Decoder\DecoderInterface;
use Chubbyphp\Deserialization\Decoder\TypeDecoderInterface;
use Chubbyphp\Mock\Call;
use Chubbyphp\Mock\MockByCallsTrait;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;

/**
 * @covers \Chubbyphp\Deserialization\Container\DecoderFactory
 *
 * @internal
 */
final class DecoderFactoryTest extends TestCase
{
    use MockByCallsTrait;

    public function testInvoke(): void
    {
        /** @var TypeDecoderInterface $typeDecoder */
        $typeDecoder = $this->getMockByCalls(TypeDecoderInterface::class, [
            Call::create('getContentType')->with()->willReturn('application/json'),
        ]);

        /** @var ContainerInterface $container */
        $container = $this->getMockByCalls(ContainerInterface::class, [
            Call::create('get')
                ->with(TypeDecoderInterface::class.'[]')
                ->willReturn([$typeDecoder]),
        ]);

        $factory = new DecoderFactory();

        $negotiator = $factory($container);

        self::assertInstanceOf(DecoderInterface::class, $negotiator);
    }
}
