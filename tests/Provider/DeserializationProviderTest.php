<?php

namespace Chubbyphp\Tests\Deserialization\Provider;

use Chubbyphp\Deserialization\Registry\ObjectMappingRegistry;
use Chubbyphp\Deserialization\Provider\DeserializationProvider;
use Chubbyphp\Deserialization\Deserializer;
use Chubbyphp\Deserialization\Transformer\JsonTransformer;
use Chubbyphp\Deserialization\Transformer\UrlEncodedTransformer;
use Chubbyphp\Deserialization\Transformer\XmlTransformer;
use Chubbyphp\Deserialization\Transformer\YamlTransformer;
use Pimple\Container;

/**
 * @covers \Chubbyphp\Deserialization\Provider\DeserializationProvider
 */
final class DeserializationProviderTest extends \PHPUnit_Framework_TestCase
{
    public function testRegister()
    {
        $container = new Container();
        $container->register(new DeserializationProvider());

        self::assertTrue(isset($container['deserializer.emptystringtonull']));
        self::assertTrue(isset($container['deserializer.objectmappings']));
        self::assertTrue(isset($container['deserializer.objectmappingregistry']));
        self::assertTrue(isset($container['deserializer']));

        self::assertTrue(isset($container['deserializer.transformer.json']));
        self::assertTrue(isset($container['deserializer.transformer.urlencoded']));
        self::assertTrue(isset($container['deserializer.transformer.xml']));
        self::assertTrue(isset($container['deserializer.transformer.yaml']));

        self::assertFalse($container['deserializer.emptystringtonull']);
        self::assertSame([], $container['deserializer.objectmappings']);
        self::assertInstanceOf(ObjectMappingRegistry::class, $container['deserializer.objectmappingregistry']);
        self::assertInstanceOf(Deserializer::class, $container['deserializer']);

        self::assertInstanceOf(JsonTransformer::class, $container['deserializer.transformer.json']);
        self::assertInstanceOf(UrlEncodedTransformer::class, $container['deserializer.transformer.urlencoded']);
        self::assertInstanceOf(XmlTransformer::class, $container['deserializer.transformer.xml']);
        self::assertInstanceOf(YamlTransformer::class, $container['deserializer.transformer.yaml']);
    }
}
