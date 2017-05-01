<?php

declare(strict_types=1);

namespace Chubbyphp\Tests\Deserialize\Deserializer;

use Chubbyphp\Deserialize\Deserializer\PropertyDeserializerCallback;

/**
 * @covers \Chubbyphp\Deserialize\Deserializer\PropertyDeserializerCallback
 */
final class PropertyDeserializerCallbackTest extends \PHPUnit_Framework_TestCase
{
    public function testDeserializePropertyWithNullValue()
    {
        $propertyDeserializer = new PropertyDeserializerCallback($this->getCallback());

        self::assertSame(
            'existingValue',
            $propertyDeserializer->deserializeProperty(null, 'existingValue')
        );
    }

    public function testDeserializePropertyWithValue()
    {
        $propertyDeserializer = new PropertyDeserializerCallback($this->getCallback());

        self::assertSame(
            'value',
            $propertyDeserializer->deserializeProperty('value', 'existingValue')
        );
    }

    /**
     * @return callable
     */
    private function getCallback(): callable
    {
        return function ($serializedValue, $existingValue) {
            if (null !== $serializedValue) {
                return $serializedValue;
            }

            return $existingValue;
        };
    }
}
