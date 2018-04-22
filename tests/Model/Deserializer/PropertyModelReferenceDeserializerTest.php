<?php

declare(strict_types=1);

namespace Chubbyphp\DeserializationModel\Tests\Deserializer;

use Chubbyphp\Deserialization\DeserializerInterface;
use Chubbyphp\DeserializationModel\Deserializer\PropertyModelReferenceDeserializer;
use Chubbyphp\Model\ModelInterface;
use Chubbyphp\Model\Reference\ModelReferenceInterface;
use Chubbyphp\Model\ResolverInterface;
use Chubbyphp\Tests\DeserializationModel\Resources\Model\Model;

/**
 * @covers \Chubbyphp\DeserializationModel\Deserializer\PropertyModelReferenceDeserializer
 */
class PropertyModelReferenceDeserializerTest extends \PHPUnit_Framework_TestCase
{
    public function testDeserializeWithoutReference()
    {
        self::expectException(\RuntimeException::class);
        self::expectExceptionMessage('Object needs to implement: Chubbyphp\Model\Reference\ModelReferenceInterface, given: NULL');

        $resolver = $this->getResolver();

        $propertyDeserializer = new PropertyModelReferenceDeserializer($resolver, Model::class);
        $propertyDeserializer->deserializeProperty('path', null);
    }

    public function testDeserializeWithoutDeserializer()
    {
        self::expectException(\RuntimeException::class);
        self::expectExceptionMessage('Deserializer needed: Chubbyphp\Deserialization\DeserializerInterface');

        $resolver = $this->getResolver();

        $modelReference = $this->getModelReference();

        $propertyDeserializer = new PropertyModelReferenceDeserializer($resolver, Model::class);
        $propertyDeserializer->deserializeProperty('path', null, $modelReference);
    }

    public function testDeserializeByArray()
    {
        $resolver = $this->getResolver();

        $modelReference = $this->getModelReference();
        $deserializer = $this->getDeserializer();

        $propertyDeserializer = new PropertyModelReferenceDeserializer($resolver, Model::class);
        $propertyDeserializer->deserializeProperty(
            'path',
            ['name' => 'name1'],
            $modelReference,
            null,
            $deserializer
        );

        self::assertInstanceOf(Model::class, $modelReference->getModel());
        self::assertSame('name1', $modelReference->getModel()->_serializedData['name']);
    }

    public function testDeserializeByArrayWithExistingReference()
    {
        $resolver = $this->getResolver();

        $model = Model::create();

        $modelReference = $this->getModelReference($model);
        $deserializer = $this->getDeserializer();

        $propertyDeserializer = new PropertyModelReferenceDeserializer($resolver, Model::class);
        $propertyDeserializer->deserializeProperty(
            'path',
            ['name' => 'name1'],
            $modelReference,
            null,
            $deserializer
        );

        self::assertInstanceOf(Model::class, $modelReference->getModel());
        self::assertSame('name1', $modelReference->getModel()->_serializedData['name']);
    }

    public function testDeserializeById()
    {
        $model = Model::create();

        $resolver = $this->getResolver([Model::class => ['id1' => $model]]);

        $modelReference = $this->getModelReference();
        $deserializer = $this->getDeserializer();

        $propertyDeserializer = new PropertyModelReferenceDeserializer($resolver, Model::class);
        $propertyDeserializer->deserializeProperty(
            'path',
            'id1',
            $modelReference,
            null,
            $deserializer
        );

        self::assertInstanceOf(Model::class, $modelReference->getModel());
        self::assertSame($model, $modelReference->getModel());
    }

    /**
     * @param ModelInterface|null $model
     *
     * @return ModelReferenceInterface
     */
    private function getModelReference(ModelInterface $model = null): ModelReferenceInterface
    {
        /** @var ModelReferenceInterface|\PHPUnit_Framework_MockObject_MockObject $modelReference */
        $modelReference = $this
            ->getMockBuilder(ModelReferenceInterface::class)
            ->setMethods(['getModel', 'setModel'])
            ->getMockForAbstractClass()
        ;

        $modelReference->_model = $model;

        $modelReference->expects(self::any())->method('getModel')->willReturnCallback(
            function () use ($modelReference) {
                return $modelReference->_model;
            }
        );

        $modelReference->expects(self::any())->method('setModel')->willReturnCallback(
            function (ModelInterface $model = null) use ($modelReference) {
                $modelReference->_model = $model;

                return $modelReference;
            }
        );

        return $modelReference;
    }

    /**
     * @param array $models
     *
     * @return ResolverInterface
     */
    private function getResolver(array $models = []): ResolverInterface
    {
        /** @var ResolverInterface|\PHPUnit_Framework_MockObject_MockObject $resolver */
        $resolver = $this
            ->getMockBuilder(ResolverInterface::class)
            ->setMethods(['find'])
            ->getMockForAbstractClass();

        $resolver->expects(self::any())->method('find')->willReturnCallback(
            function (string $modelClass, string $id = null) use ($models) {
                if (isset($models[$modelClass][$id])) {
                    return $models[$modelClass][$id];
                }
            }
        );

        return $resolver;
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
