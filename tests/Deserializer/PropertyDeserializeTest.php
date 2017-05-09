<?php

declare(strict_types=1);

namespace Chubbyphp\Tests\Deserialization\Deserializer;

use Chubbyphp\Deserialization\Deserializer\PropertyDeserializer;

/**
 * @covers \Chubbyphp\Deserialization\Deserializer\PropertyDeserializer
 */
final class PropertyDeserializerTest extends \PHPUnit_Framework_TestCase
{
    public function testDeserializeProperty()
    {
        $propertyDeserializer = new PropertyDeserializer();

        self::assertSame(
            'value',
            $propertyDeserializer->deserializeProperty('path', 'value')
        );
    }
}
