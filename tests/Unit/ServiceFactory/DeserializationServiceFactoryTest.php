<?php

declare(strict_types=1);

namespace Chubbyphp\Tests\Deserialization\Unit\ServiceFactory;

use Chubbyphp\Container\Container;
use Chubbyphp\DecodeEncode\Decoder\Decoder;
use Chubbyphp\Deserialization\Denormalizer\Denormalizer;
use Chubbyphp\Deserialization\Denormalizer\DenormalizerObjectMappingRegistry;
use Chubbyphp\Deserialization\Deserializer;
use Chubbyphp\Deserialization\Mapping\DenormalizationFieldMappingFactory;
use Chubbyphp\Deserialization\ServiceFactory\DeserializationServiceFactory;
use Chubbyphp\Mock\MockObjectBuilder;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;

/**
 * @covers \Chubbyphp\Deserialization\ServiceFactory\DeserializationServiceFactory
 *
 * @internal
 */
final class DeserializationServiceFactoryTest extends TestCase
{
    public function testRegister(): void
    {
        $container = new Container();
        $factory = new DeserializationServiceFactory();
        $container->factories($factory());

        self::assertTrue($container->has('deserializer'));

        self::assertTrue($container->has('deserializer.decoder'));
        self::assertTrue($container->has('deserializer.decodertypes'));

        self::assertTrue($container->has('deserializer.denormalizer'));
        self::assertTrue($container->has('deserializer.denormalizer.fieldmappingfactory'));
        self::assertTrue($container->has('deserializer.denormalizer.objectmappingregistry'));
        self::assertTrue($container->has('deserializer.denormalizer.objectmappings'));

        self::assertInstanceOf(Deserializer::class, $container->get('deserializer'));

        self::assertInstanceOf(Decoder::class, $container->get('deserializer.decoder'));
        self::assertIsArray($container->get('deserializer.decodertypes'));

        self::assertInstanceOf(
            DenormalizationFieldMappingFactory::class,
            $container->get('deserializer.denormalizer.fieldmappingfactory')
        );

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
        $builder = new MockObjectBuilder();

        /** @var LoggerInterface $logger */
        $logger = $builder->create(LoggerInterface::class, []);

        $container = new Container([
            'logger' => static fn () => $logger,
        ]);

        $factory = new DeserializationServiceFactory();

        $container->factories($factory());

        /** @var Denormalizer $denormalizer */
        $denormalizer = $container->get('deserializer.denormalizer');

        self::assertInstanceOf(Denormalizer::class, $denormalizer);

        $reflectionProperty = new \ReflectionProperty($denormalizer, 'logger');
        $reflectionProperty->setAccessible(true);

        self::assertSame($logger, $reflectionProperty->getValue($denormalizer));
    }
}
