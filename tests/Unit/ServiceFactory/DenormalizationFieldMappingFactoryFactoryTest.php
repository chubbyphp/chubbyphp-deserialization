<?php

declare(strict_types=1);

namespace Chubbyphp\Tests\Deserialization\Unit\ServiceFactory;

use Chubbyphp\Deserialization\Mapping\DenormalizationFieldMappingFactoryInterface;
use Chubbyphp\Deserialization\ServiceFactory\DenormalizationFieldMappingFactoryFactory;
use Chubbyphp\Mock\MockByCallsTrait;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;

/**
 * @covers \Chubbyphp\Deserialization\ServiceFactory\DenormalizationFieldMappingFactoryFactory
 *
 * @internal
 */
final class DenormalizationFieldMappingFactoryFactoryTest extends TestCase
{
    use MockByCallsTrait;

    public function testInvoke(): void
    {
        /** @var ContainerInterface $container */
        $container = $this->getMockByCalls(ContainerInterface::class, []);

        $factory = new DenormalizationFieldMappingFactoryFactory();

        $service = $factory($container);

        self::assertInstanceOf(DenormalizationFieldMappingFactoryInterface::class, $service);
    }

    public function testCallStatic(): void
    {
        /** @var ContainerInterface $container */
        $container = $this->getMockByCalls(ContainerInterface::class, []);

        $factory = [DenormalizationFieldMappingFactoryFactory::class, 'default'];

        $service = $factory($container);

        self::assertInstanceOf(DenormalizationFieldMappingFactoryInterface::class, $service);
    }
}
