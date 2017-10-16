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

        $object = $denormalizer->denormalize(get_class($this->getObject()), ['name' => 'name']);

        self::assertSame('name', $object->getName());
    }

    public function testDenormalizeWithExisting()
    {
        $denormalizer = new Denormalizer($this->getDenormalizerObjectMappingRegistry([
            $this->getDenormalizationObjectMapping(),
        ]));

        $object = $denormalizer->denormalize($this->getObject(), ['name' => 'name']);

        self::assertSame('name', $object->getName());
    }

    public function testDenormalizeWithAdditionalData()
    {
        self::expectException(DeserializerRuntimeException::class);
        self::expectExceptionMessage('There are additional field(s) at paths: "value"');

        $denormalizer = new Denormalizer($this->getDenormalizerObjectMappingRegistry([
            $this->getDenormalizationObjectMapping(),
        ]));

        $denormalizer->denormalize(get_class($this->getObject()), ['name' => 'name', 'value' => 'value']);
    }

    public function testDenormalizeWithAdditionalDataAndAllowIt()
    {
        $denormalizer = new Denormalizer($this->getDenormalizerObjectMappingRegistry([
            $this->getDenormalizationObjectMapping(),
        ]));

        $object = $denormalizer->denormalize(
            get_class($this->getObject()),
            ['name' => 'name', 'value' => 'value'],
            $this->getDenormalizerContext(true)
        );

        self::assertSame('name', $object->getName());
    }

    public function testDenormalizeWithMissingObjectMapping()
    {
        self::expectException(DeserializerLogicException::class);

        $denormalizer = new Denormalizer($this->getDenormalizerObjectMappingRegistry([]));

        $denormalizer->denormalize(get_class($this->getObject()), ['name' => 'name']);
    }

    public function testDenormalizeWithNoData()
    {
        $denormalizer = new Denormalizer($this->getDenormalizerObjectMappingRegistry([
            $this->getDenormalizationObjectMapping(),
        ]));

        $object = $denormalizer->denormalize(get_class($this->getObject()), []);

        self::assertNull($object->getName());
    }

    public function testDenormalizeWithGroups()
    {
        $denormalizer = new Denormalizer($this->getDenormalizerObjectMappingRegistry([
            $this->getDenormalizationObjectMapping(['read']),
        ]));

        $object = $denormalizer->denormalize(
            get_class($this->getObject()),
            ['name' => 'name'],
            $this->getDenormalizerContext(false, ['read'])
        );

        self::assertSame('name', $object->getName());
    }

    public function testDenormalizeWithGroupsButNoGroupOnField()
    {
        $denormalizer = new Denormalizer($this->getDenormalizerObjectMappingRegistry([
            $this->getDenormalizationObjectMapping(),
        ]));

        $object = $denormalizer->denormalize(
            get_class($this->getObject()),
            ['name' => 'name'],
            $this->getDenormalizerContext(false, ['read'])
        );

        self::assertNull($object->getName());
    }

    /**
     * @param DenormalizationObjectMappingInterface[] $denormalizationObjectMappings
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
     * @param array $groups
     *
     * @return DenormalizationObjectMappingInterface
     */
    private function getDenormalizationObjectMapping(array $groups = []): DenormalizationObjectMappingInterface
    {
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

        $objectMapping->expects(self::any())->method('getDenormalizationFactory')->willReturnCallback(function () use ($object) {
            return function () use ($object) {
                return clone $object;
            };
        });

        $objectMapping->expects(self::any())->method('getDenormalizationFieldMappings')->willReturn([
            $this->getDenormalizationFieldMapping($groups),
        ]);

        return $objectMapping;
    }

    /**
     * @param array $groups
     *
     * @return DenormalizationFieldMappingInterface
     */
    private function getDenormalizationFieldMapping(array $groups = []): DenormalizationFieldMappingInterface
    {
        /** @var DenormalizationFieldMappingInterface|\PHPUnit_Framework_MockObject_MockObject $fieldMapping */
        $fieldMapping = $this->getMockBuilder(DenormalizationFieldMappingInterface::class)
            ->setMethods([])
            ->getMockForAbstractClass();

        $fieldMapping->expects(self::any())->method('getName')->willReturn('name');
        $fieldMapping->expects(self::any())->method('getGroups')->willReturn($groups);
        $fieldMapping->expects(self::any())->method('getFieldDenormalizer')->willReturn($this->getFieldDenormalizer());

        return $fieldMapping;
    }

    /**
     * @return FieldDenormalizerInterface
     */
    private function getFieldDenormalizer(): FieldDenormalizerInterface
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
        ) {
            $object->setName($value);
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
        bool $allowedAdditionalFields = false,
        array $groups = []
    ): DenormalizerContextInterface {
        /** @var DenormalizerContextInterface|\PHPUnit_Framework_MockObject_MockObject $context */
        $context = $this->getMockBuilder(DenormalizerContextInterface::class)
            ->setMethods([])
            ->getMockForAbstractClass();

        $context->expects(self::any())->method('isAllowedAdditionalFields')->willReturn($allowedAdditionalFields);
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
            private $name;

            /**
             * @return string|null
             */
            public function getName()
            {
                return $this->name;
            }

            /**
             * @param string $name
             *
             * @return self
             */
            public function setName(string $name): self
            {
                $this->name = $name;

                return $this;
            }
        };
    }
}
