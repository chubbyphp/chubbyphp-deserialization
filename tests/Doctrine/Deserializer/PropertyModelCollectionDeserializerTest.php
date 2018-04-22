<?php

declare(strict_types=1);

namespace Chubbyphp\DeserializationDoctrine\Tests\Deserializer;

use Chubbyphp\Deserialization\DeserializerInterface;
use Chubbyphp\DeserializationDoctrine\Deserializer\PropertyModelCollectionDeserializer;
use Chubbyphp\Tests\DeserializationDoctrine\Resources\Model\Model;
use Doctrine\Common\Collections\Collection;

/**
 * @covers \Chubbyphp\DeserializationDoctrine\Deserializer\PropertyModelCollectionDeserializer
 */
class PropertyModelCollectionDeserializerTest extends \PHPUnit_Framework_TestCase
{
    public function testDeserializeWithoutCollection()
    {
        self::expectException(\RuntimeException::class);
        self::expectExceptionMessage('Object needs to implement: Doctrine\Common\Collections\Collection, given: NULL');

        $propertyDeserializer = new PropertyModelCollectionDeserializer(Model::class);
        $propertyDeserializer->deserializeProperty('path', []);
    }

    public function testDeserializeWithoutDeserializer()
    {
        self::expectException(\RuntimeException::class);
        self::expectExceptionMessage('Deserializer needed: Chubbyphp\Deserialization\DeserializerInterface');

        $collection = $this->getCollection();

        $propertyDeserializer = new PropertyModelCollectionDeserializer(Model::class);
        $propertyDeserializer->deserializeProperty('path', [], $collection);
    }

    public function testDeserialize()
    {
        $collection = $this->getCollection([Model::create('id1')]);
        $deserializer = $this->getDeserializer();

        $propertyDeserializer = new PropertyModelCollectionDeserializer(Model::class);
        $propertyDeserializer->deserializeProperty(
            'path',
            [['name' => 'name1'], ['name' => 'name2']],
            $collection,
            null,
            $deserializer
        );

        $entities = $collection->getValues();

        self::assertCount(2, $entities);

        self::assertInstanceOf(Model::class, $entities[0]);
        self::assertSame('name1', $entities[0]->_serializedData['name']);

        self::assertInstanceOf(Model::class, $entities[1]);
        self::assertSame('name2', $entities[1]->_serializedData['name']);
    }

    /**
     * @param ModelInterface[]
     *
     * @return Collection
     */
    private function getCollection(array $entities = []): Collection
    {
        /** @var Collection|\PHPUnit_Framework_MockObject_MockObject $collection */
        $collection = $this
            ->getMockBuilder(Collection::class)
            ->setMethods(['getValues', 'clear', 'set'])
            ->getMockForAbstractClass()
        ;

        $collection->_models = $entities;

        $collection->expects(self::any())->method('getValues')->willReturnCallback(
            function () use ($collection) {
                return $collection->_models;
            }
        );

        $collection->expects(self::any())->method('clear')->willReturnCallback(
            function () use ($collection) {
                return $collection->_models = [];
            }
        );

        $collection->expects(self::any())->method('set')->willReturnCallback(
            function ($key, $value) use ($collection) {
                $collection->_models[$key] = $value;

                return $collection;
            }
        );

        return $collection;
    }

    /**
     * @return DeserializerInterface
     */
    private function getDeserializer(): DeserializerInterface
    {
        /** @var DeserializerInterface|\PHPUnit_Framework_MockObject_MockObject $deserializer */
        $deserializer = $this
            ->getMockBuilder(DeserializerInterface::class)
            ->setMethods(['deserializeByClass', 'deserializeByObject'])
            ->getMockForAbstractClass();

        $deserializer->expects(self::any())->method('deserializeByClass')->willReturnCallback(
            function (array $serializedData, string $class, string $path = '') {
                $object = (new \ReflectionClass($class))->newInstanceWithoutConstructor();
                $object->_serializedData = $serializedData;

                return $object;
            }
        );

        $deserializer->expects(self::any())->method('deserializeByObject')->willReturnCallback(
            function (array $serializedData, $object, string $path = '') {
                $object->_serializedData = $serializedData;

                return $object;
            }
        );

        return $deserializer;
    }
}
