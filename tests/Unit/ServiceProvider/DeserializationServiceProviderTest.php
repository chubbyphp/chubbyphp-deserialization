<?php

declare(strict_types=1);

namespace Chubbyphp\Tests\Deserialization\Unit\ServiceProvider;

use Chubbyphp\Deserialization\Decoder\Decoder;
use Chubbyphp\Deserialization\Decoder\JsonTypeDecoder;
use Chubbyphp\Deserialization\Decoder\JsonxTypeDecoder;
use Chubbyphp\Deserialization\Decoder\UrlEncodedTypeDecoder;
use Chubbyphp\Deserialization\Decoder\XmlTypeDecoder;
use Chubbyphp\Deserialization\Decoder\YamlTypeDecoder;
use Chubbyphp\Deserialization\Denormalizer\Denormalizer;
use Chubbyphp\Deserialization\Denormalizer\DenormalizerObjectMappingRegistry;
use Chubbyphp\Deserialization\Deserializer;
use Chubbyphp\Deserialization\ServiceProvider\DeserializationServiceProvider;
use Chubbyphp\Mock\MockByCallsTrait;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Pimple\Container;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;

/**
 * @covers \Chubbyphp\Deserialization\ServiceProvider\DeserializationServiceProvider
 *
 * @internal
 */
final class DeserializationProviderTest extends TestCase
{
    use MockByCallsTrait;

    public function testRegister(): void
    {
        $container = new Container();
        $container->register(new DeserializationServiceProvider());

        error_clear_last();

        self::assertTrue(isset($container['deserializer']));

        self::assertTrue(isset($container['deserializer.decoder']));
        self::assertTrue(isset($container['deserializer.decodertypes']));

        self::assertTrue(isset($container['deserializer.denormalizer']));
        self::assertTrue(isset($container['deserializer.denormalizer.objectmappingregistry']));
        self::assertTrue(isset($container['deserializer.denormalizer.objectmappings']));

        self::assertInstanceOf(Deserializer::class, $container['deserializer']);

        self::assertInstanceOf(Decoder::class, $container['deserializer.decoder']);
        self::assertIsArray($container['deserializer.decodertypes']);
        self::assertInstanceOf(JsonTypeDecoder::class, $container['deserializer.decodertypes'][0]);
        self::assertInstanceOf(JsonxTypeDecoder::class, $container['deserializer.decodertypes'][1]);
        self::assertInstanceOf(UrlEncodedTypeDecoder::class, $container['deserializer.decodertypes'][2]);
        self::assertInstanceOf(XmlTypeDecoder::class, $container['deserializer.decodertypes'][3]);
        self::assertInstanceOf(YamlTypeDecoder::class, $container['deserializer.decodertypes'][4]);

        self::assertInstanceOf(
            DenormalizerObjectMappingRegistry::class,
            $container['deserializer.denormalizer.objectmappingregistry']
        );

        self::assertIsArray($container['deserializer.denormalizer.objectmappings']);

        /** @var Denormalizer $denormalizer */
        $denormalizer = $container['deserializer.denormalizer'];

        self::assertInstanceOf(Denormalizer::class, $denormalizer);

        $reflectionProperty = new \ReflectionProperty($denormalizer, 'logger');
        $reflectionProperty->setAccessible(true);

        self::assertInstanceOf(NullLogger::class, $reflectionProperty->getValue($denormalizer));

        $error = error_get_last();

        self::assertNotNull($error);

        self::assertSame(E_USER_DEPRECATED, $error['type']);
        self::assertSame(
            'Register the decoder types by yourself:'
                .' $container[\'deserializer.decodertypes\'] = static function () { return [new JsonTypeDecoder()]; };',
            $error['message']
        );
    }

    public function testRegisterWithDefinedLogger(): void
    {
        /** @var LoggerInterface|MockObject $logger */
        $logger = $this->getMockByCalls(LoggerInterface::class);

        $container = new Container([
            'logger' => $logger,
        ]);

        $container->register(new DeserializationServiceProvider());

        /** @var Denormalizer $denormalizer */
        $denormalizer = $container['deserializer.denormalizer'];

        self::assertInstanceOf(Denormalizer::class, $denormalizer);

        $reflectionProperty = new \ReflectionProperty($denormalizer, 'logger');
        $reflectionProperty->setAccessible(true);

        self::assertSame($logger, $reflectionProperty->getValue($denormalizer));
    }
}
