<?php

declare(strict_types=1);

namespace Chubbyphp\Tests\Deserialization\Denormalizer;

use Chubbyphp\Deserialization\Denormalizer\Denormalizer;
use Chubbyphp\Deserialization\Denormalizer\DenormalizerContextInterface;
use Chubbyphp\Deserialization\Denormalizer\DenormalizerInterface;
use Chubbyphp\Deserialization\Denormalizer\DenormalizerObjectMappingRegistryInterface;
use Chubbyphp\Deserialization\Denormalizer\FieldDenormalizerInterface;
use Chubbyphp\Deserialization\DeserializerLogicException;
use Chubbyphp\Deserialization\DeserializerRuntimeException;
use Chubbyphp\Deserialization\Mapping\DenormalizationFieldMappingInterface;
use Chubbyphp\Deserialization\Mapping\DenormalizationObjectMappingInterface;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Chubbyphp\Deserialization\Denormalizer\Denormalizer
 */
class DenormalizerTest extends TestCase
{
    public function testDenormalizeWithNew()
    {
        $denormalizer = new Denormalizer($this->getDenormalizerObjectMappingRegistry([
            $this->getDenormalizationObjectMapping(),
        ]));

        $object = $denormalizer->denormalize(get_class($this->getObject()), [
            'typeString' => 'typeString',
            'typeInteger' => 5,
            'typeFloat' => 5.5,
            'typeBool' => true,
        ]);

        self::assertSame('typeString', $object->getTypeString());
        self::assertSame(5, $object->getTypeInteger());
        self::assertSame(5.5, $object->getTypeFloat());
    }

    public function testDenormalizeWithNewForcedTypes()
    {
        $denormalizer = new Denormalizer($this->getDenormalizerObjectMappingRegistry([
            $this->getDenormalizationObjectMapping(),
        ]));

        $object = $denormalizer->denormalize(get_class($this->getObject()), [
            'typeString' => 'typeString',
            'typeInteger' => '5',
            'typeFloat' => 5.5,
            'typeBool' => true,
        ]);

        self::assertSame('typeString', $object->getTypeString());
        self::assertSame(5, $object->getTypeInteger());
        self::assertSame(5.5, $object->getTypeFloat());
    }

    public function testDenormalizeWithNewForcedTypesAndNotForceableValues()
    {
        $denormalizer = new Denormalizer($this->getDenormalizerObjectMappingRegistry([
            $this->getDenormalizationObjectMapping(),
        ]));

        $object = $denormalizer->denormalize(get_class($this->getObject()), [
            'typeString' => 'typeString',
            'typeInteger' => 5.5,
            'typeFloat' => '5cars',
        ]);

        self::assertSame('typeString', $object->getTypeString());
        self::assertSame(5.5, $object->getTypeInteger());
        self::assertSame('5cars', $object->getTypeFloat());
    }

    public function testDenormalizeWithNewAndType()
    {
        $denormalizer = new Denormalizer($this->getDenormalizerObjectMappingRegistry([
            $this->getDenormalizationObjectMapping(),
        ]));

        $object = $denormalizer->denormalize(get_class($this->getObject()), ['typeString' => 'typeString', '_type' => 'object']);

        self::assertSame('typeString', $object->getTypeString());
    }

    public function testDenormalizeWithExisting()
    {
        $denormalizer = new Denormalizer($this->getDenormalizerObjectMappingRegistry([
            $this->getDenormalizationObjectMapping(),
        ]));

        $object = $denormalizer->denormalize($this->getObject(), ['typeString' => 'typeString']);

        self::assertSame('typeString', $object->getTypeString());
    }

    public function testDenormalizeWithDataContainsNumericKeys()
    {
        self::expectException(DeserializerRuntimeException::class);
        self::expectExceptionMessage('There are additional field(s) at paths: "0"');

        $denormalizer = new Denormalizer($this->getDenormalizerObjectMappingRegistry([
            $this->getDenormalizationObjectMapping(),
        ]));

        $denormalizer->denormalize(get_class($this->getObject()), ['test'], $this->getDenormalizerContext([]));
    }

    public function testDenormalizeWithNotWorkingFactory()
    {
        self::expectException(DeserializerLogicException::class);
        self::expectExceptionMessage('Factory does not return object, "string" given at path: ""');

        $denormalizer = new Denormalizer($this->getDenormalizerObjectMappingRegistry([
            $this->getDenormalizationObjectMapping(
                [],
                function () {
                    return 'string';
                }
            ),
        ]));

        $denormalizer->denormalize(get_class($this->getObject()), ['typeString' => 'typeString', '_type' => 'object']);
    }

    public function testDenormalizeWithAdditionalData()
    {
        self::expectException(DeserializerRuntimeException::class);
        self::expectExceptionMessage('There are additional field(s) at paths: "value"');

        $denormalizer = new Denormalizer($this->getDenormalizerObjectMappingRegistry([
            $this->getDenormalizationObjectMapping(),
        ]));

        $denormalizer->denormalize(
            get_class($this->getObject()),
            ['typeString' => 'typeString', 'value' => 'value'],
            $this->getDenormalizerContext([])
        );
    }

    public function testDenormalizeWithAdditionalDataAndAllowIt()
    {
        $denormalizer = new Denormalizer($this->getDenormalizerObjectMappingRegistry([
            $this->getDenormalizationObjectMapping(),
        ]));

        $object = $denormalizer->denormalize(
            get_class($this->getObject()),
            ['typeString' => 'typeString', 'value' => 'value']
        );

        self::assertSame('typeString', $object->getTypeString());
    }

    public function testDenormalizeWithMissingObjectMapping()
    {
        self::expectException(DeserializerLogicException::class);

        $denormalizer = new Denormalizer($this->getDenormalizerObjectMappingRegistry([]));

        $denormalizer->denormalize(get_class($this->getObject()), ['typeString' => 'typeString']);
    }

    public function testDenormalizeWithNoData()
    {
        $denormalizer = new Denormalizer($this->getDenormalizerObjectMappingRegistry([
            $this->getDenormalizationObjectMapping(),
        ]));

        $object = $denormalizer->denormalize(get_class($this->getObject()), []);

        self::assertNull($object->getTypeString());
    }

    public function testDenormalizeWithGroups()
    {
        $denormalizer = new Denormalizer($this->getDenormalizerObjectMappingRegistry([
            $this->getDenormalizationObjectMapping(['read']),
        ]));

        $object = $denormalizer->denormalize(
            get_class($this->getObject()),
            ['typeString' => 'typeString'],
            $this->getDenormalizerContext(null, ['read'])
        );

        self::assertSame('typeString', $object->getTypeString());
    }

    public function testDenormalizeWithGroupsButNoGroupOnField()
    {
        $denormalizer = new Denormalizer($this->getDenormalizerObjectMappingRegistry([
            $this->getDenormalizationObjectMapping(),
        ]));

        $object = $denormalizer->denormalize(
            get_class($this->getObject()),
            ['typeString' => 'typeString'],
            $this->getDenormalizerContext(null, ['read'])
        );

        self::assertNull($object->getTypeString());
    }

    /**
     * @param DenormalizationObjectMappingInterface[] $denormalizationObjectMappings
     *
     * @return DenormalizerObjectMappingRegistryInterface
     */
    private function getDenormalizerObjectMappingRegistry(array $denormalizationObjectMappings): DenormalizerObjectMappingRegistryInterface
    {
        /** @var DenormalizerObjectMappingRegistryInterface|\PHPUnit_Framework_MockObject_MockObject $objectMappingRegistry */
        $objectMappingRegistry = $this->getMockBuilder(DenormalizerObjectMappingRegistryInterface::class)
            ->setMethods(['getObjectMapping'])
            ->getMockForAbstractClass();

        $objectMappingRegistry->__mapppings = [];

        foreach ($denormalizationObjectMappings as $denormalizationObjectMapping) {
            $objectMappingRegistry->__mapppings[$denormalizationObjectMapping->getClass()] = $denormalizationObjectMapping;
        }

        $objectMappingRegistry->expects(self::any())->method('getObjectMapping')->willReturnCallback(
            function (string $class) use ($objectMappingRegistry) {
                if (isset($objectMappingRegistry->__mapppings[$class])) {
                    return $objectMappingRegistry->__mapppings[$class];
                }

                throw DeserializerLogicException::createMissingMapping($class);
            }
        );

        return $objectMappingRegistry;
    }

    /**
     * @param array         $groups
     * @param callable|null $factory
     *
     * @return DenormalizationObjectMappingInterface
     */
    private function getDenormalizationObjectMapping(
        array $groups = [],
        callable $factory = null
    ): DenormalizationObjectMappingInterface {
        /** @var DenormalizationObjectMappingInterface|\PHPUnit_Framework_MockObject_MockObject $objectMapping */
        $objectMapping = $this->getMockBuilder(DenormalizationObjectMappingInterface::class)
            ->setMethods([])
            ->getMockForAbstractClass();

        $object = $this->getObject();

        $objectMapping->expects(self::any())->method('getClass')->willReturnCallback(
            function () use ($object) {
                return get_class($object);
            }
        );

        if (null === $factory) {
            $factory = function () use ($object) {
                return clone $object;
            };
        }

        $objectMapping->expects(self::any())->method('getDenormalizationFactory')->willReturnCallback(function () use ($object, $factory) {
            return $factory;
        });

        $objectMapping->expects(self::any())->method('getDenormalizationFieldMappings')->willReturn([
            $this->getDenormalizationFieldMapping('typeString', $groups),
            $this->getDenormalizationFieldMappingWithForceType(
                'typeInteger',
                $groups,
                DenormalizationFieldMappingInterface::FORCETYPE_INT
            ),
            $this->getDenormalizationFieldMappingWithForceType(
                'typeFloat',
                $groups,
                'unknownType'
            ),
            $this->getDenormalizationFieldMappingWithForceType('typeBool', $groups),
        ]);

        return $objectMapping;
    }

    /**
     * @param string      $name
     * @param array       $groups
     * @param string|null $forceType
     *
     * @return DenormalizationFieldMappingInterface
     */
    private function getDenormalizationFieldMapping(
        string $name,
        array $groups = []
    ): DenormalizationFieldMappingInterface {
        /** @var DenormalizationFieldMappingInterface|\PHPUnit_Framework_MockObject_MockObject $fieldMapping */
        $fieldMapping = $this->getMockBuilder(DenormalizationFieldMappingInterface::class)
            ->getMockForAbstractClass();

        $fieldMapping->expects(self::any())->method('getName')->willReturn($name);
        $fieldMapping->expects(self::any())->method('getGroups')->willReturn($groups);
        $fieldMapping->expects(self::any())->method('getFieldDenormalizer')->willReturn($this->getFieldDenormalizer($name));

        return $fieldMapping;
    }

    /**
     * @param string      $name
     * @param array       $groups
     * @param string|null $forceType
     *
     * @return DenormalizationFieldMappingInterface
     */
    private function getDenormalizationFieldMappingWithForceType(
        string $name,
        array $groups = [],
        string $forceType = null
    ): DenormalizationFieldMappingInterface {
        /** @var DenormalizationFieldMappingInterface|\PHPUnit_Framework_MockObject_MockObject $fieldMapping */
        $fieldMapping = $this->getMockBuilder(DenormalizationFieldMappingInterface::class)
            ->setMethods(['getTypeString', 'getGroups', 'getFieldDenormalizer', 'getForceType'])
            ->getMockForAbstractClass();

        $fieldMapping->expects(self::any())->method('getName')->willReturn($name);
        $fieldMapping->expects(self::any())->method('getGroups')->willReturn($groups);
        $fieldMapping->expects(self::any())->method('getFieldDenormalizer')->willReturn($this->getFieldDenormalizer($name));
        $fieldMapping->expects(self::any())->method('getForceType')->willReturn($forceType);

        return $fieldMapping;
    }

    /**
     * @param string $name
     *
     * @return FieldDenormalizerInterface
     */
    private function getFieldDenormalizer(string $name): FieldDenormalizerInterface
    {
        /** @var FieldDenormalizerInterface|\PHPUnit_Framework_MockObject_MockObject $fieldDenormalizer */
        $fieldDenormalizer = $this->getMockBuilder(FieldDenormalizerInterface::class)
            ->setMethods([])
            ->getMockForAbstractClass();

        $fieldDenormalizer->expects(self::any())->method('denormalizeField')->willReturnCallback(function (
            string $path,
            $object,
            $value,
            DenormalizerContextInterface $context,
            DenormalizerInterface $denormalizer = null
        ) use ($name) {
            $method = 'set'.ucfirst($name);
            $object->$method($value);
        });

        return $fieldDenormalizer;
    }

    /**
     * @param bool  $allowedAdditionalFields
     * @param array $groups
     *
     * @return DenormalizerContextInterface
     */
    private function getDenormalizerContext(
        array $allowedAdditionalFields = null,
        array $groups = []
    ): DenormalizerContextInterface {
        /** @var DenormalizerContextInterface|\PHPUnit_Framework_MockObject_MockObject $context */
        $context = $this->getMockBuilder(DenormalizerContextInterface::class)
            ->setMethods([])
            ->getMockForAbstractClass();

        $context->expects(self::any())->method('getAllowedAdditionalFields')->willReturn($allowedAdditionalFields);
        $context->expects(self::any())->method('getGroups')->willReturn($groups);

        return $context;
    }

    /**
     * @return object
     */
    private function getObject()
    {
        return new class() {
            /**
             * @var string
             */
            private $typeString;

            /**
             * @var int
             */
            private $typeInteger;

            /**
             * @var float
             */
            private $typeFloat;

            /**
             * @var bool
             */
            private $typeBool;

            /**
             * @return string|null
             */
            public function getTypeString()
            {
                return $this->typeString;
            }

            /**
             * @param string $typeString
             *
             * @return self
             */
            public function setTypeString($typeString): self
            {
                $this->typeString = $typeString;

                return $this;
            }

            /**
             * @return int
             */
            public function getTypeInteger()
            {
                return $this->typeInteger;
            }

            /**
             * @param int $typeInteger
             *
             * @return self
             */
            public function setTypeInteger($typeInteger): self
            {
                $this->typeInteger = $typeInteger;

                return $this;
            }

            /**
             * @return float
             */
            public function getTypeFloat()
            {
                return $this->typeFloat;
            }

            /**
             * @param float $typeFloat
             *
             * @return self
             */
            public function setTypeFloat($typeFloat): self
            {
                $this->typeFloat = $typeFloat;

                return $this;
            }

            /**
             * @return bool
             */
            public function isTypeBool()
            {
                return $this->typeBool;
            }

            /**
             * @param bool $typeBool
             *
             * @return self
             */
            public function setTypeBool($typeBool): self
            {
                $this->typeBool = $typeBool;

                return $this;
            }
        };
    }
}
