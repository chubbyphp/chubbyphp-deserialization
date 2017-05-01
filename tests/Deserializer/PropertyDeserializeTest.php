<?php

declare(strict_types=1);

namespace Chubbyphp\Tests\Deserialize\Deserializer;

use Chubbyphp\Deserialize\Deserializer\PropertyDeserializer;

/**
 * @covers \Chubbyphp\Deserialize\Deserializer\PropertyDeserializer
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
