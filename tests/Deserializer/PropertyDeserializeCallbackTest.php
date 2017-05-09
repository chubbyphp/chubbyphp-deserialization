<?php

declare(strict_types=1);

namespace Chubbyphp\Tests\Deserialization\Deserializer;

use Chubbyphp\Deserialization\Deserializer\PropertyDeserializerCallback;

/**
 * @covers \Chubbyphp\Deserialization\Deserializer\PropertyDeserializerCallback
 */
final class PropertyDeserializerCallbackTest extends \PHPUnit_Framework_TestCase
{
    public function testDeserializePropertyWithNullValue()
    {
        $propertyDeserializer = new PropertyDeserializerCallback($this->getCallback());

        self::assertSame(
            'existingValue',
            $propertyDeserializer->deserializeProperty('path', null, 'existingValue')
        );
    }

    public function testDeserializePropertyWithValue()
    {
        $propertyDeserializer = new PropertyDeserializerCallback($this->getCallback());

        self::assertSame(
            'value',
            $propertyDeserializer->deserializeProperty('path', 'value', 'existingValue')
        );
    }

    /**
     * @return callable
     */
    private function getCallback(): callable
    {
        return function (string $path, $serializedValue, $existingValue) {
            if (null !== $serializedValue) {
                return $serializedValue;
            }

            return $existingValue;
        };
    }
}
