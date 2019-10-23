<?php

declare(strict_types=1);

namespace Chubbyphp\Tests\Deserialization\Unit\Provider;

use Chubbyphp\Deserialization\Decoder\Decoder;
use Chubbyphp\Deserialization\Decoder\JsonTypeDecoder;
use Chubbyphp\Deserialization\Decoder\JsonxTypeDecoder;
use Chubbyphp\Deserialization\Decoder\TypeDecoderInterface;
use Chubbyphp\Deserialization\Decoder\UrlEncodedTypeDecoder;
use Chubbyphp\Deserialization\Decoder\XmlTypeDecoder;
use Chubbyphp\Deserialization\Decoder\YamlTypeDecoder;
use Chubbyphp\Deserialization\Denormalizer\Denormalizer;
use Chubbyphp\Deserialization\Denormalizer\DenormalizerObjectMappingRegistry;
use Chubbyphp\Deserialization\Deserializer;
use Chubbyphp\Deserialization\Provider\DeserializationProvider;
use Chubbyphp\Mock\MockByCallsTrait;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Pimple\Container;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;

/**
 * @covers \Chubbyphp\Deserialization\Provider\DeserializationProvider
 *
 * @internal
 */
final class DeserializationProviderTest extends TestCase
{
    use MockByCallsTrait;

    public function testRegister(): void
    {
        $container = new Container();
        $container->register(new DeserializationProvider());

        self::assertTrue(isset($container['deserializer']));

        self::assertTrue(isset($container['deserializer.decoder']));
        self::assertTrue(isset($container['deserializer.decodertypes']));

        self::assertTrue(isset($container['deserializer.denormalizer']));
        self::assertTrue(isset($container['deserializer.denormalizer.objectmappingregistry']));
        self::assertTrue(isset($container['deserializer.denormalizer.objectmappings']));

        self::assertInstanceOf(Deserializer::class, $container['deserializer']);

        self::assertInstanceOf(Decoder::class, $container['deserializer.decoder']);
        self::assertIsArray($container['deserializer.decodertypes']);

        /** @var array<int, TypeDecoderInterface> $decoderTypes */
        $decoderTypes = $container['deserializer.decodertypes'];

        self::assertInstanceOf(JsonTypeDecoder::class, array_shift($decoderTypes));

        $jsonxTypeDecoder1 = array_shift($decoderTypes);
        self::assertInstanceOf(JsonxTypeDecoder::class, $jsonxTypeDecoder1);

        self::assertSame('application/x-jsonx', $jsonxTypeDecoder1->getContentType());

        $jsonxTypeDecoder2 = array_shift($decoderTypes);
        self::assertInstanceOf(JsonxTypeDecoder::class, $jsonxTypeDecoder2);

        self::assertSame('application/jsonx+xml', $jsonxTypeDecoder2->getContentType());

        self::assertInstanceOf(UrlEncodedTypeDecoder::class, array_shift($decoderTypes));
        self::assertInstanceOf(XmlTypeDecoder::class, array_shift($decoderTypes));
        self::assertInstanceOf(YamlTypeDecoder::class, array_shift($decoderTypes));

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
    }

    public function testRegisterWithDefinedLogger(): void
    {
        /** @var LoggerInterface|MockObject $logger */
        $logger = $this->getMockByCalls(LoggerInterface::class);

        $container = new Container([
            'logger' => $logger,
        ]);

        $container->register(new DeserializationProvider());

        /** @var Denormalizer $denormalizer */
        $denormalizer = $container['deserializer.denormalizer'];

        self::assertInstanceOf(Denormalizer::class, $denormalizer);

        $reflectionProperty = new \ReflectionProperty($denormalizer, 'logger');
        $reflectionProperty->setAccessible(true);

        self::assertSame($logger, $reflectionProperty->getValue($denormalizer));
    }
}
