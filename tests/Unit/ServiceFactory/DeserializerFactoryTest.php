<?php

declare(strict_types=1);

namespace Chubbyphp\Tests\Deserialization\Unit\ServiceFactory;

use Chubbyphp\DecodeEncode\Decoder\DecoderInterface;
use Chubbyphp\Deserialization\Denormalizer\DenormalizerInterface;
use Chubbyphp\Deserialization\DeserializerInterface;
use Chubbyphp\Deserialization\ServiceFactory\DeserializerFactory;
use Chubbyphp\Mock\MockMethod\WithReturn;
use Chubbyphp\Mock\MockObjectBuilder;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;

/**
 * @covers \Chubbyphp\Deserialization\ServiceFactory\DeserializerFactory
 *
 * @internal
 */
final class DeserializerFactoryTest extends TestCase
{
    public function testInvoke(): void
    {
        $builder = new MockObjectBuilder();

        /** @var DecoderInterface $decoder */
        $decoder = $builder->create(DecoderInterface::class, []);

        /** @var DenormalizerInterface $denormalizer */
        $denormalizer = $builder->create(DenormalizerInterface::class, []);

        /** @var ContainerInterface $container */
        $container = $builder->create(ContainerInterface::class, [
            new WithReturn('has', [DecoderInterface::class], true),
            new WithReturn('get', [DecoderInterface::class], $decoder),
            new WithReturn('has', [DenormalizerInterface::class], true),
            new WithReturn('get', [DenormalizerInterface::class], $denormalizer),
        ]);

        $factory = new DeserializerFactory();

        $service = $factory($container);

        self::assertInstanceOf(DeserializerInterface::class, $service);
    }

    public function testCallStatic(): void
    {
        $builder = new MockObjectBuilder();

        $decoderKey = DecoderInterface::class.'default';
        $denormalizerKey = DenormalizerInterface::class.'default';

        /** @var DecoderInterface $decoder */
        $decoder = $builder->create(DecoderInterface::class, []);

        /** @var DenormalizerInterface $denormalizer */
        $denormalizer = $builder->create(DenormalizerInterface::class, []);

        /** @var ContainerInterface $container */
        $container = $builder->create(ContainerInterface::class, [
            new WithReturn('has', [$decoderKey], true),
            new WithReturn('get', [$decoderKey], $decoder),
            new WithReturn('has', [$denormalizerKey], true),
            new WithReturn('get', [$denormalizerKey], $denormalizer),
        ]);

        $factory = [DeserializerFactory::class, 'default'];

        $service = $factory($container);

        self::assertInstanceOf(DeserializerInterface::class, $service);
    }
}
