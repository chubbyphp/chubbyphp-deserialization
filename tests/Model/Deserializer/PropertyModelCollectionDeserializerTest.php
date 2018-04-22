<?php

declare(strict_types=1);

namespace Chubbyphp\DeserializationModel\Tests\Deserializer;

use Chubbyphp\Deserialization\DeserializerInterface;
use Chubbyphp\DeserializationModel\Deserializer\PropertyModelCollectionDeserializer;
use Chubbyphp\Model\Collection\ModelCollectionInterface;
use Chubbyphp\Tests\DeserializationModel\Resources\Model\Model;

/**
 * @covers \Chubbyphp\DeserializationModel\Deserializer\PropertyModelCollectionDeserializer
 */
class PropertyModelCollectionDeserializerTest extends \PHPUnit_Framework_TestCase
{
    public function testDeserializeWithoutCollection()
    {
        self::expectException(\RuntimeException::class);
        self::expectExceptionMessage('Object needs to implement: Chubbyphp\Model\Collection\ModelCollectionInterface, given: NULL');

        $propertyDeserializer = new PropertyModelCollectionDeserializer(Model::class);
        $propertyDeserializer->deserializeProperty('path', []);
    }

    public function testDeserializeWithoutDeserializer()
    {
        self::expectException(\RuntimeException::class);
        self::expectExceptionMessage('Deserializer needed: Chubbyphp\Deserialization\DeserializerInterface');

        $modelCollection = $this->getModelCollection();

        $propertyDeserializer = new PropertyModelCollectionDeserializer(Model::class);
        $propertyDeserializer->deserializeProperty('path', [], $modelCollection);
    }

    public function testDeserialize()
    {
        $model1 = Model::create('id1');

        $modelCollection = $this->getModelCollection([$model1]);
        $deserializer = $this->getDeserializer();

        $propertyDeserializer = new PropertyModelCollectionDeserializer(Model::class);
        $propertyDeserializer->deserializeProperty(
            'path',
            [['name' => 'name1'], ['name' => 'name2']],
            $modelCollection,
            null,
            $deserializer
        );

        $models = $modelCollection->getModels();

        self::assertCount(2, $models);

        self::assertInstanceOf(Model::class, $models[0]);
        self::assertSame($model1, $models[0]);
        self::assertSame('name1', $models[0]->_serializedData['name']);

        self::assertInstanceOf(Model::class, $models[1]);
        self::assertSame('name2', $models[1]->_serializedData['name']);
    }

    public function testDeserializeReplace()
    {
        $model1 = Model::create('id1');

        $modelCollection = $this->getModelCollection([$model1]);
        $deserializer = $this->getDeserializer();

        $propertyDeserializer = new PropertyModelCollectionDeserializer(Model::class, true);
        $propertyDeserializer->deserializeProperty(
            'path',
            [['name' => 'name1'], ['name' => 'name2']],
            $modelCollection,
            null,
            $deserializer
        );

        $models = $modelCollection->getModels();

        self::assertCount(2, $models);

        self::assertInstanceOf(Model::class, $models[0]);
        self::assertNotSame($model1, $models[0]);
        self::assertSame('name1', $models[0]->_serializedData['name']);

        self::assertInstanceOf(Model::class, $models[1]);
        self::assertSame('name2', $models[1]->_serializedData['name']);
    }

    /**
     * @param ModelInterface[]
     *
     * @return ModelCollectionInterface
     */
    private function getModelCollection(array $models = []): ModelCollectionInterface
    {
        /** @var ModelCollectionInterface|\PHPUnit_Framework_MockObject_MockObject $modelCollection */
        $modelCollection = $this
            ->getMockBuilder(ModelCollectionInterface::class)
            ->setMethods(['getModels', 'setModels'])
            ->getMockForAbstractClass()
        ;

        $modelCollection->_models = $models;

        $modelCollection->expects(self::any())->method('getModels')->willReturnCallback(
            function () use ($modelCollection) {
                return $modelCollection->_models;
            }
        );

        $modelCollection->expects(self::any())->method('setModels')->willReturnCallback(
            function (array $models) use ($modelCollection) {
                $modelCollection->_models = $models;

                return $modelCollection;
            }
        );

        return $modelCollection;
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
