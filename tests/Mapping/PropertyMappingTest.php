<?php

declare(strict_types=1);

namespace Chubbyphp\Tests\Deserialize\Mapping;

use Chubbyphp\Deserialize\Deserializer\PropertyDeserializerInterface;
use Chubbyphp\Deserialize\Mapping\PropertyMapping;

/**
 * @covers \Chubbyphp\Deserialize\Mapping\PropertyMapping
 */
final class PropertyMappingTest extends \PHPUnit_Framework_TestCase
{
    public function test()
    {
        $name = 'property1';
        $propertyDeserializer = $this->getPropertyDeserializer();

        $propertyMapping = new PropertyMapping($name, $propertyDeserializer);

        self::assertSame($name, $propertyMapping->getName());
        self::assertSame($propertyDeserializer, $propertyMapping->getPropertyDeserializer());
    }

    /**
     * @return PropertyDeserializerInterface
     */
    private function getPropertyDeserializer(): PropertyDeserializerInterface
    {
        return $this->getMockBuilder(PropertyDeserializerInterface::class)->getMockForAbstractClass();
    }
}
