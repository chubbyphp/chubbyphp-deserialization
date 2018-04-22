<?php

declare(strict_types=1);

namespace Chubbyphp\DeserializationDoctrine\Tests\Deserializer;

use Chubbyphp\Deserialization\DeserializerInterface;
use Chubbyphp\DeserializationDoctrine\Deserializer\PropertyModelReferenceDeserializer;
use Chubbyphp\Tests\DeserializationDoctrine\Resources\Model\Model;
use Chubbyphp\Tests\DeserializationDoctrine\Resources\Model\ProxyModel;
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\Persistence\ObjectRepository;

/**
 * @covers \Chubbyphp\DeserializationDoctrine\Deserializer\PropertyModelReferenceDeserializer
 */
class PropertyModelReferenceDeserializerTest extends \PHPUnit_Framework_TestCase
{
    public function testDeserializeWithoutDeserializer()
    {
        self::expectException(\RuntimeException::class);
        self::expectExceptionMessage('Deserializer needed: Chubbyphp\Deserialization\DeserializerInterface');

        $managerRegistry = $this->getManagerRegistry($this->getManager($this->getRepository([])));

        $propertyDeserializer = new PropertyModelReferenceDeserializer($managerRegistry, Model::class);
        $propertyDeserializer->deserializeProperty('path', null);
    }

    public function testDeserializeByArray()
    {
        $managerRegistry = $this->getManagerRegistry($this->getManager($this->getRepository([])));

        $deserializer = $this->getDeserializer();

        $propertyDeserializer = new PropertyModelReferenceDeserializer($managerRegistry, Model::class);

        $newEntity = $propertyDeserializer->deserializeProperty(
            'path',
            ['name' => 'name1'],
            null,
            null,
            $deserializer
        );

        self::assertInstanceOf(Model::class, $newEntity);
        self::assertSame('name1', $newEntity->_serializedData['name']);
    }

    public function testDeserializeByArrayWithExistingReference()
    {
        $managerRegistry = $this->getManagerRegistry($this->getManager($this->getRepository([])));

        $entity = Model::create();

        $deserializer = $this->getDeserializer();

        $propertyDeserializer = new PropertyModelReferenceDeserializer($managerRegistry, Model::class);

        $newEntity = $propertyDeserializer->deserializeProperty(
            'path',
            ['name' => 'name1'],
            $entity,
            null,
            $deserializer
        );

        self::assertInstanceOf(Model::class, $newEntity);
        self::assertSame($entity, $newEntity);
        self::assertSame('name1', $newEntity->_serializedData['name']);
    }

    public function testDeserializeByArrayWithExistingProxyReference()
    {
        $managerRegistry = $this->getManagerRegistry($this->getManager($this->getRepository([])));

        $entity = ProxyModel::create();

        $deserializer = $this->getDeserializer();

        $propertyDeserializer = new PropertyModelReferenceDeserializer($managerRegistry, Model::class);

        $newEntity = $propertyDeserializer->deserializeProperty(
            'path',
            ['name' => 'name1'],
            $entity,
            null,
            $deserializer
        );

        self::assertInstanceOf(Model::class, $newEntity);
        self::assertSame($entity, $newEntity);
        self::assertSame('name1', $newEntity->_serializedData['name']);
    }

    public function testDeserializeById()
    {
        $entity = Model::create();

        $managerRegistry = $this->getManagerRegistry($this->getManager($this->getRepository(['id1' => $entity])));

        $deserializer = $this->getDeserializer();

        $propertyDeserializer = new PropertyModelReferenceDeserializer($managerRegistry, Model::class);

        $newEntity = $propertyDeserializer->deserializeProperty(
            'path',
            'id1',
            $entity,
            null,
            $deserializer
        );

        self::assertInstanceOf(Model::class, $entity);
        self::assertSame($entity, $newEntity);
    }

    /**
     * @param ObjectManager $manager
     *
     * @return ManagerRegistry
     */
    private function getManagerRegistry(ObjectManager $manager): ManagerRegistry
    {
        /** @var ManagerRegistry|\PHPUnit_Framework_MockObject_MockObject $managerRegistry */
        $managerRegistry = $this
            ->getMockBuilder(ManagerRegistry::class)
            ->setMethods(['getManagerForClass'])
            ->getMockForAbstractClass()
        ;

        $managerRegistry->expects(self::any())->method('getManagerForClass')->willReturnCallback(
            function (string $class) use ($manager) {
                return $manager;
            }
        );

        return $managerRegistry;
    }

    /**
     * @param ObjectRepository $repository
     *
     * @return ObjectManager
     */
    private function getManager(ObjectRepository $repository): ObjectManager
    {
        /** @var ObjectManager|\PHPUnit_Framework_MockObject_MockObject $manager */
        $manager = $this
            ->getMockBuilder(ObjectManager::class)
            ->setMethods(['getRepository'])
            ->getMockForAbstractClass()
        ;

        $manager->expects(self::any())->method('getRepository')->willReturnCallback(
            function (string $class) use ($repository) {
                return $repository;
            }
        );

        return $manager;
    }

    /**
     * @param array $entities
     *
     * @return ObjectRepository
     */
    private function getRepository(array $entities): ObjectRepository
    {
        /** @var ObjectRepository|\PHPUnit_Framework_MockObject_MockObject $repository */
        $repository = $this
            ->getMockBuilder(ObjectRepository::class)
            ->setMethods(['find'])
            ->getMockForAbstractClass()
        ;

        $repository->expects(self::any())->method('find')->willReturnCallback(
            function ($id) use ($entities) {
                if (isset($entities[$id])) {
                    return $entities[$id];
                }
            }
        );

        return $repository;
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
