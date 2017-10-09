<?php

namespace Chubbyphp\Tests\Deserialization\Provider;

use Chubbyphp\Deserialization\Decoder\Decoder;
use Chubbyphp\Deserialization\Decoder\JsonDecoderType;
use Chubbyphp\Deserialization\Decoder\UrlEncodedDecoderType;
use Chubbyphp\Deserialization\Decoder\XmlDecoderType;
use Chubbyphp\Deserialization\Decoder\YamlDecoderType;
use Chubbyphp\Deserialization\Denormalizer\Denormalizer;
use Chubbyphp\Deserialization\Provider\DeserializationProvider;
use Chubbyphp\Deserialization\Deserializer;
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

        self::assertTrue(isset($container['deserializer.denormalizer.objectmappings']));
        self::assertTrue(isset($container['deserializer.denormalizer']));

        self::assertInstanceOf(Deserializer::class, $container['deserializer']);

        self::assertInstanceOf(Decoder::class, $container['deserializer.decoder']);
        self::assertInternalType('array', $container['deserializer.decodertypes']);
        self::assertInstanceOf(JsonDecoderType::class, $container['deserializer.decodertypes'][0]);
        self::assertInstanceOf(UrlEncodedDecoderType::class, $container['deserializer.decodertypes'][1]);
        self::assertInstanceOf(XmlDecoderType::class, $container['deserializer.decodertypes'][2]);
        self::assertInstanceOf(YamlDecoderType::class, $container['deserializer.decodertypes'][3]);

        self::assertInstanceOf(Denormalizer::class, $container['deserializer.denormalizer']);
        self::assertInternalType('array', $container['deserializer.denormalizer.objectmappings']);
    }
}
