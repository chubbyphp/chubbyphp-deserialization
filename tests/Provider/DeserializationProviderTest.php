<?php

namespace Chubbyphp\Tests\Deserialization\Provider;

use Chubbyphp\Deserialization\Registry\ObjectMappingRegistry;
use Chubbyphp\Deserialization\Provider\DeserializationProvider;
use Chubbyphp\Deserialization\Deserializer;
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

        self::assertFalse($container['deserializer.emptystringtonull']);
        self::assertSame([], $container['deserializer.objectmappings']);
        self::assertInstanceOf(ObjectMappingRegistry::class, $container['deserializer.objectmappingregistry']);
        self::assertInstanceOf(Deserializer::class, $container['deserializer']);
    }
}
