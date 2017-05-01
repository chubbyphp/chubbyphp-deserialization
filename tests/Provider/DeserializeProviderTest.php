<?php

namespace Chubbyphp\Tests\Deserialize\Provider;

use Chubbyphp\Deserialize\Registry\ObjectMappingRegistry;
use Chubbyphp\Deserialize\Provider\DeserializeProvider;
use Chubbyphp\Deserialize\Deserializer;
use Pimple\Container;

/**
 * @covers \Chubbyphp\Deserialize\Provider\DeserializeProvider
 */
final class DeserializeProviderTest extends \PHPUnit_Framework_TestCase
{
    public function testRegister()
    {
        $container = new Container();
        $container->register(new DeserializeProvider());

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
