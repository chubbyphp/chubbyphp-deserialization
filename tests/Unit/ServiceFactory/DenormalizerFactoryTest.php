<?php

declare(strict_types=1);

namespace Chubbyphp\Tests\Deserialization\Unit\ServiceFactory;

use Chubbyphp\Deserialization\Denormalizer\DenormalizerInterface;
use Chubbyphp\Deserialization\Denormalizer\DenormalizerObjectMappingRegistryInterface;
use Chubbyphp\Deserialization\ServiceFactory\DenormalizerFactory;
use Chubbyphp\Mock\MockMethod\WithReturn;
use Chubbyphp\Mock\MockObjectBuilder;
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
    public function testInvoke(): void
    {
        $builder = new MockObjectBuilder();

        /** @var DenormalizerObjectMappingRegistryInterface $normalizerObjectMappingRegistry */
        $normalizerObjectMappingRegistry = $builder->create(DenormalizerObjectMappingRegistryInterface::class, []);

        /** @var ContainerInterface $container */
        $container = $builder->create(ContainerInterface::class, [
            new WithReturn('has', [DenormalizerObjectMappingRegistryInterface::class], true),
            new WithReturn('get', [DenormalizerObjectMappingRegistryInterface::class], $normalizerObjectMappingRegistry),
            new WithReturn('has', [LoggerInterface::class], false),
        ]);

        $factory = new DenormalizerFactory();

        $service = $factory($container);

        self::assertInstanceOf(DenormalizerInterface::class, $service);
    }

    public function testCallStatic(): void
    {
        $builder = new MockObjectBuilder();

        /** @var DenormalizerObjectMappingRegistryInterface $normalizerObjectMappingRegistry */
        $normalizerObjectMappingRegistry = $builder->create(DenormalizerObjectMappingRegistryInterface::class, []);

        /** @var ContainerInterface $container */
        $container = $builder->create(ContainerInterface::class, [
            new WithReturn('has', [DenormalizerObjectMappingRegistryInterface::class.'default'], true),
            new WithReturn('get', [DenormalizerObjectMappingRegistryInterface::class.'default'], $normalizerObjectMappingRegistry),
            new WithReturn('has', [LoggerInterface::class], false),
        ]);
        $factory = [DenormalizerFactory::class, 'default'];

        $service = $factory($container);

        self::assertInstanceOf(DenormalizerInterface::class, $service);
    }
}
