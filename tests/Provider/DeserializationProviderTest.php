<?php

namespace Chubbyphp\Tests\Deserialization\Provider;

use Chubbyphp\Deserialization\Decoder\Decoder;
use Chubbyphp\Deserialization\Decoder\JsonTypeDecoder;
use Chubbyphp\Deserialization\Decoder\JsonxTypeDecoder;
use Chubbyphp\Deserialization\Decoder\UrlEncodedTypeDecoder;
use Chubbyphp\Deserialization\Decoder\XmlTypeDecoder;
use Chubbyphp\Deserialization\Decoder\YamlTypeDecoder;
use Chubbyphp\Deserialization\Denormalizer\Denormalizer;
use Chubbyphp\Deserialization\Denormalizer\DenormalizerObjectMappingRegistry;
use Chubbyphp\Deserialization\Deserializer;
use Chubbyphp\Deserialization\Provider\DeserializationProvider;
use PHPUnit\Framework\TestCase;
use Pimple\Container;

/**
 * @covers \Chubbyphp\Deserialization\Provider\DeserializationProvider
 */
final class DeserializationProviderTest extends TestCase
{
    public function testRegister()
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
        self::assertInternalType('array', $container['deserializer.decodertypes']);
        self::assertInstanceOf(JsonTypeDecoder::class, $container['deserializer.decodertypes'][0]);
        self::assertInstanceOf(JsonxTypeDecoder::class, $container['deserializer.decodertypes'][1]);
        self::assertInstanceOf(UrlEncodedTypeDecoder::class, $container['deserializer.decodertypes'][2]);
        self::assertInstanceOf(XmlTypeDecoder::class, $container['deserializer.decodertypes'][3]);
        self::assertInstanceOf(YamlTypeDecoder::class, $container['deserializer.decodertypes'][4]);

        self::assertInstanceOf(Denormalizer::class, $container['deserializer.denormalizer']);
        self::assertInstanceOf(DenormalizerObjectMappingRegistry::class, $container['deserializer.denormalizer.objectmappingregistry']);
        self::assertInternalType('array', $container['deserializer.denormalizer.objectmappings']);
    }
}
