<?php

declare(strict_types=1);

namespace Chubbyphp\Tests\Deserialization\Unit\ServiceFactory;

use Chubbyphp\Container\Container;
use Chubbyphp\Deserialization\Decoder\Decoder;
use Chubbyphp\Deserialization\Denormalizer\Denormalizer;
use Chubbyphp\Deserialization\Denormalizer\DenormalizerObjectMappingRegistry;
use Chubbyphp\Deserialization\Deserializer;
use Chubbyphp\Deserialization\ServiceFactory\DeserializationServiceFactory;
use Chubbyphp\Mock\MockByCallsTrait;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;

/**
 * @covers \Chubbyphp\Deserialization\ServiceFactory\DeserializationServiceFactory
 *
 * @internal
 */
final class DeserializationProviderTest extends TestCase
{
    use MockByCallsTrait;

    public function testRegister(): void
    {
        $container = new Container();
        $container->factories((new DeserializationServiceFactory())($container));

        self::assertTrue($container->has('deserializer'));

        self::assertTrue($container->has('deserializer.decoder'));
        self::assertTrue($container->has('deserializer.decodertypes'));

        self::assertTrue($container->has('deserializer.denormalizer'));
        self::assertTrue($container->has('deserializer.denormalizer.objectmappingregistry'));
        self::assertTrue($container->has('deserializer.denormalizer.objectmappings'));

        self::assertInstanceOf(Deserializer::class, $container->get('deserializer'));

        self::assertInstanceOf(Decoder::class, $container->get('deserializer.decoder'));
        self::assertIsArray($container->get('deserializer.decodertypes'));

        self::assertInstanceOf(
            DenormalizerObjectMappingRegistry::class,
            $container->get('deserializer.denormalizer.objectmappingregistry')
        );

        self::assertIsArray($container->get('deserializer.denormalizer.objectmappings'));

        /** @var Denormalizer $denormalizer */
        $denormalizer = $container->get('deserializer.denormalizer');

        self::assertInstanceOf(Denormalizer::class, $denormalizer);

        $reflectionProperty = new \ReflectionProperty($denormalizer, 'logger');
        $reflectionProperty->setAccessible(true);

        self::assertInstanceOf(NullLogger::class, $reflectionProperty->getValue($denormalizer));
    }

    public function testRegisterWithDefinedLogger(): void
    {
        /** @var LoggerInterface|MockObject $logger */
        $logger = $this->getMockByCalls(LoggerInterface::class);

        $container = new Container([
            'logger' => function () use ($logger) {
                return $logger;
            },
        ]);

        $container->factories((new DeserializationServiceFactory())($container));

        /** @var Denormalizer $denormalizer */
        $denormalizer = $container->get('deserializer.denormalizer');

        self::assertInstanceOf(Denormalizer::class, $denormalizer);

        $reflectionProperty = new \ReflectionProperty($denormalizer, 'logger');
        $reflectionProperty->setAccessible(true);

        self::assertSame($logger, $reflectionProperty->getValue($denormalizer));
    }
}
