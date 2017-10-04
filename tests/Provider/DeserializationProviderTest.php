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
        self::assertTrue(isset($container['deserializer.decodertype.json']));
        self::assertTrue(isset($container['deserializer.decodertype.urlencoded']));
        self::assertTrue(isset($container['deserializer.decodertype.xml']));
        self::assertTrue(isset($container['deserializer.decodertype.yaml']));

        self::assertTrue(isset($container['deserializer.denormalizer.objectmappings']));
        self::assertTrue(isset($container['deserializer.denormalizer']));

        self::assertInstanceOf(Deserializer::class, $container['deserializer']);

        self::assertInstanceOf(Decoder::class, $container['deserializer.decoder']);
        self::assertInstanceOf(JsonDecoderType::class, $container['deserializer.decodertype.json']);
        self::assertInstanceOf(UrlEncodedDecoderType::class, $container['deserializer.decodertype.urlencoded']);
        self::assertInstanceOf(XmlDecoderType::class, $container['deserializer.decodertype.xml']);
        self::assertInstanceOf(YamlDecoderType::class, $container['deserializer.decodertype.yaml']);

        self::assertInstanceOf(Denormalizer::class, $container['deserializer.denormalizer']);
        self::assertInternalType('array', $container['deserializer.denormalizer.objectmappings']);
    }
}
