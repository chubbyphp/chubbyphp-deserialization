<?php


namespace Chubbyphp\Tests\Deserialize\Deserializer;

use Chubbyphp\Deserialize\Deserializer;
use Chubbyphp\Deserialize\Deserializer\PropertyDeserializerInterface;
use Chubbyphp\Deserialize\DeserializerInterface;
use Chubbyphp\Deserialize\Mapping\ObjectMappingInterface;
use Chubbyphp\Deserialize\Mapping\PropertyMappingInterface;
use Chubbyphp\Deserialize\NotObjectException;
use Chubbyphp\Deserialize\Registry\ObjectMappingRegistryInterface;
use Chubbyphp\Tests\Deserialize\Resources\Model;
use Psr\Log\LoggerInterface;
use Psr\Log\LogLevel;

class DeserializerTest extends \PHPUnit_Framework_TestCase
{
    public function testWithClass()
    {
        $factory = function () { return new Model(); };

        $objectMappingRegistry = $this->getObjectMappingRegistry([
            Model::class => $this->getObjectMapping(
                Model::class,
                $factory,
                [
                    $this->getPropertyMapping('name', $this->getPropertyDeserializer())
                ]
            )
        ]);

        $logger = $this->getLogger();

        $deserializer = new Deserializer($objectMappingRegistry, false, $logger);

        /** @var Model $model */
        $model = $deserializer->deserializeByClass([
            'name' => 'name1',
            'unknownProperty' => 'dummy'
        ], Model::class);

        self::assertInstanceOf(Model::class, $model);
        self::assertSame('name1', $model->getName());

        self::assertEquals([
            [
                'level' => LogLevel::INFO,
                'message' => 'deserialize: property {property} of class {class}',
                'context' => [
                    'class' => Model::class,
                    'property' => 'name',
                ]
            ],
            [
                'level' => LogLevel::NOTICE,
                'message' => 'deserialize: no mapping for property {property} of class {class}',
                'context' => [
                    'class' => Model::class,
                    'property' => 'unknownProperty',
                ]
            ],
        ], $logger->__logs);
    }

    public function testWithObject()
    {
        $factory = function () { return new Model(); };

        $objectMappingRegistry = $this->getObjectMappingRegistry([
            Model::class => $this->getObjectMapping(
                Model::class,
                $factory,
                [
                    $this->getPropertyMapping('name', $this->getPropertyDeserializer())
                ]
            )
        ]);

        $logger = $this->getLogger();

        $deserializer = new Deserializer($objectMappingRegistry, false, $logger);

        $model = new Model();
        $model->setName('name1');

        /** @var Model $updatedModel */
        $updatedModel = $deserializer->deserializeByObject([
            'name' => 'name2'
        ], $model);

        self::assertSame($model, $updatedModel);
        self::assertSame('name2', $updatedModel->getName());

        self::assertEquals([
            [
                'level' => LogLevel::INFO,
                'message' => 'deserialize: property {property} of class {class}',
                'context' => [
                    'class' => Model::class,
                    'property' => 'name',
                ]
            ],
        ], $logger->__logs);
    }

    public function testWithArray()
    {
        self::expectException(NotObjectException::class);
        self::expectExceptionMessage('Input is not an object, type array given');

        $objectMappingRegistry = $this->getObjectMappingRegistry([]);

        $logger = $this->getLogger();

        $deserializer = new Deserializer($objectMappingRegistry, false, $logger);

        $model = [];

        $deserializer->deserializeByObject([
            'name' => 'name2'
        ], $model);
    }

    public function testEmptyStringToNull()
    {
        $factory = function () { return new Model(); };

        $objectMappingRegistry = $this->getObjectMappingRegistry([
            Model::class => $this->getObjectMapping(
                Model::class,
                $factory,
                [
                    $this->getPropertyMapping('name', $this->getPropertyDeserializer())
                ]
            )
        ]);

        $logger = $this->getLogger();

        $deserializer = new Deserializer($objectMappingRegistry, true, $logger);

        /** @var Model $model */
        $model = $deserializer->deserializeByClass([
            'name' => ''
        ], Model::class);

        self::assertInstanceOf(Model::class, $model);
        self::assertNull($model->getName());
    }

    /**
     * @param ObjectMappingInterface[] $mappings
     * @return ObjectMappingRegistryInterface
     */
    private function getObjectMappingRegistry(array $mappings): ObjectMappingRegistryInterface {
        /** @var ObjectMappingRegistryInterface|\PHPUnit_Framework_MockObject_MockObject $registry */
        $registry = $this
            ->getMockBuilder(ObjectMappingRegistryInterface::class)
            ->setMethods(['getObjectMappingForClass'])
            ->getMockForAbstractClass()
        ;

        $registry->expects(self::any())->method('getObjectMappingForClass')->willReturnCallback(
            function (string $class) use ($mappings) {
                if (isset($mappings[$class])) {
                    return $mappings[$class];
                }

                return null;
            }
        );

        return $registry;
    }

    /**
     * @param string $class
     * @param callable $factory
     * @param array $propertyMappings
     * @return ObjectMappingInterface
     */
    private function getObjectMapping(
        string $class,
        callable $factory,
        array $propertyMappings
    ): ObjectMappingInterface {
        /** @var ObjectMappingInterface|\PHPUnit_Framework_MockObject_MockObject $mapping */
        $mapping = $this
            ->getMockBuilder(ObjectMappingInterface::class)
            ->setMethods(['getClass', 'getFactory', 'getPropertyMappings'])
            ->getMockForAbstractClass()
        ;

        $mapping->expects(self::any())->method('getClass')->willReturn($class);
        $mapping->expects(self::any())->method('getFactory')->willReturn($factory);
        $mapping->expects(self::any())->method('getPropertyMappings')->willReturn($propertyMappings);

        return $mapping;
    }

    /**
     * @param string $name
     * @param PropertyDeserializerInterface $propertyDeserializer
     * @return PropertyMappingInterface
     */
    private function getPropertyMapping(
        string $name,
        PropertyDeserializerInterface $propertyDeserializer
    ): PropertyMappingInterface {
        /** @var PropertyMappingInterface|\PHPUnit_Framework_MockObject_MockObject $mapping */
        $mapping = $this
            ->getMockBuilder(PropertyMappingInterface::class)
            ->setMethods(['getName', 'getPropertyDeserializer'])
            ->getMockForAbstractClass()
        ;

        $mapping->expects(self::any())->method('getName')->willReturn($name);
        $mapping->expects(self::any())->method('getPropertyDeserializer')->willReturn($propertyDeserializer);

        return $mapping;
    }

    /**
     * @return PropertyDeserializerInterface
     */
    private function getPropertyDeserializer(): PropertyDeserializerInterface
    {
        /** @var PropertyDeserializerInterface|\PHPUnit_Framework_MockObject_MockObject $propertyDeserializer */
        $propertyDeserializer = $this
            ->getMockBuilder(PropertyDeserializerInterface::class)
            ->setMethods(['deserializeProperty'])
            ->getMockForAbstractClass()
        ;

        $propertyDeserializer->expects(self::any())->method('deserializeProperty')->willReturnCallback(
            function(
                $serializedValue,
                $existingValue = null,
                $object = null,
                DeserializerInterface $deserializer = null
            ) {
                    return $serializedValue;
            }
        );

        return $propertyDeserializer;
    }

        /**
     * @return LoggerInterface
     */
    private function getLogger(): LoggerInterface
    {
        $methods = [
            'emergency',
            'alert',
            'critical',
            'error',
            'warning',
            'notice',
            'info',
            'debug',
        ];

        /** @var LoggerInterface|\PHPUnit_Framework_MockObject_MockObject $logger */
        $logger = $this
            ->getMockBuilder(LoggerInterface::class)
            ->setMethods(array_merge($methods, ['log']))
            ->getMockForAbstractClass()
        ;

        $logger->__logs = [];

        foreach ($methods as $method) {
            $logger
                ->expects(self::any())
                ->method($method)
                ->willReturnCallback(
                    function (string $message, array $context = []) use ($logger, $method) {
                        $logger->log($method, $message, $context);
                    }
                )
            ;
        }

        $logger
            ->expects(self::any())
            ->method('log')
            ->willReturnCallback(
                function (string $level, string $message, array $context = []) use ($logger) {
                    $logger->__logs[] = ['level' => $level, 'message' => $message, 'context' => $context];
                }
            )
        ;

        return $logger;
    }
}
