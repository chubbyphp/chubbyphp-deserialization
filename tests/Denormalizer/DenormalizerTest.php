<?php

declare(strict_types=1);

namespace Chubbyphp\Tests\Deserialization\Denormalizer;

use Chubbyphp\Deserialization\Denormalizer\Denormalizer;
use Chubbyphp\Deserialization\Denormalizer\DenormalizerContextBuilder;
use Chubbyphp\Deserialization\Denormalizer\DenormalizerContextInterface;
use Chubbyphp\Deserialization\Denormalizer\DenormalizerInterface;
use Chubbyphp\Deserialization\Denormalizer\DenormalizerObjectMappingRegistryInterface;
use Chubbyphp\Deserialization\Denormalizer\FieldDenormalizerInterface;
use Chubbyphp\Deserialization\DeserializerLogicException;
use Chubbyphp\Deserialization\DeserializerRuntimeException;
use Chubbyphp\Deserialization\Mapping\DenormalizationFieldMappingInterface;
use Chubbyphp\Deserialization\Mapping\DenormalizationObjectMappingInterface;
use Chubbyphp\Deserialization\Policy\PolicyInterface;
use Chubbyphp\Mock\Argument\ArgumentInstanceOf;
use Chubbyphp\Mock\Call;
use Chubbyphp\Mock\MockByCallsTrait;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;

/**
 * @covers \Chubbyphp\Deserialization\Denormalizer\Denormalizer
 *
 * @internal
 */
class DenormalizerTest extends TestCase
{
    use MockByCallsTrait;

    public function testDenormalizeWithNew()
    {
        $object = new \stdClass();

        $factory = function () use ($object) {
            return $object;
        };

        /** @var DenormalizerContextInterface|MockObject $context */
        $context = $this->getMockByCalls(DenormalizerContextInterface::class, [
            Call::create('getGroups')->with()->willReturn([]),
            Call::create('getAllowedAdditionalFields')->with()->willReturn(null),
        ]);

        /** @var FieldDenormalizerInterface|MockObject $nameFieldDenormalizer */
        $nameFieldDenormalizer = $this->getMockByCalls(FieldDenormalizerInterface::class, [
            Call::create('denormalizeField')
                ->with('name', $object, 'name', $context, new ArgumentInstanceOf(DenormalizerInterface::class)),
        ]);

        /** @var DenormalizationFieldMappingInterface|MockObject $denormalizationNameFieldMapping */
        $denormalizationNameFieldMapping = $this->getMockByCalls(DenormalizationFieldMappingInterface::class, [
            Call::create('getName')->with()->willReturn('name'),
            Call::create('getFieldDenormalizer')->with()->willReturn($nameFieldDenormalizer),
        ]);

        /** @var DenormalizationFieldMappingInterface|MockObject $denormalizationValueFieldMapping */
        $denormalizationValueFieldMapping = $this->getMockByCalls(DenormalizationFieldMappingInterface::class, [
            Call::create('getName')->with()->willReturn('value'),
        ]);

        /** @var DenormalizationObjectMappingInterface|MockObject $objectMapping */
        $objectMapping = $this->getMockByCalls(DenormalizationObjectMappingInterface::class, [
            Call::create('getDenormalizationFactory')->with('', null)->willReturn($factory),
            Call::create('getDenormalizationFieldMappings')->with('', null)->willReturn([
                $denormalizationNameFieldMapping,
                $denormalizationValueFieldMapping,
            ]),
        ]);

        /** @var DenormalizerObjectMappingRegistryInterface|MockObject $registry */
        $registry = $this->getMockByCalls(DenormalizerObjectMappingRegistryInterface::class, [
            Call::create('getObjectMapping')->with(\stdClass::class)->willReturn($objectMapping),
        ]);

        /** @var LoggerInterface|MockObject $logger */
        $logger = $this->getMockByCalls(LoggerInterface::class, [
            Call::create('info')->with('deserialize: path {path}', ['path' => 'name']),
        ]);

        $denormalizer = new Denormalizer($registry, $logger);

        self::assertSame($object, $denormalizer->denormalize(\stdClass::class, ['name' => 'name'], $context));
    }

    public function testDenormalizeWithNewAndType()
    {
        $object = new \stdClass();

        $factory = function () use ($object) {
            return $object;
        };

        /** @var DenormalizerContextInterface|MockObject $context */
        $context = $this->getMockByCalls(DenormalizerContextInterface::class, [
            Call::create('getGroups')->with()->willReturn([]),
            Call::create('getAllowedAdditionalFields')->with()->willReturn(null),
        ]);

        /** @var FieldDenormalizerInterface|MockObject $nameFieldDenormalizer */
        $nameFieldDenormalizer = $this->getMockByCalls(FieldDenormalizerInterface::class, [
            Call::create('denormalizeField')
                ->with('name', $object, 'name', $context, new ArgumentInstanceOf(DenormalizerInterface::class)),
        ]);

        /** @var DenormalizationFieldMappingInterface|MockObject $denormalizationNameFieldMapping */
        $denormalizationNameFieldMapping = $this->getMockByCalls(DenormalizationFieldMappingInterface::class, [
            Call::create('getName')->with()->willReturn('name'),
            Call::create('getFieldDenormalizer')->with()->willReturn($nameFieldDenormalizer),
        ]);

        /** @var DenormalizationFieldMappingInterface|MockObject $denormalizationValueFieldMapping */
        $denormalizationValueFieldMapping = $this->getMockByCalls(DenormalizationFieldMappingInterface::class, [
            Call::create('getName')->with()->willReturn('value'),
        ]);

        /** @var DenormalizationObjectMappingInterface|MockObject $objectMapping */
        $objectMapping = $this->getMockByCalls(DenormalizationObjectMappingInterface::class, [
            Call::create('getDenormalizationFactory')->with('', 'object')->willReturn($factory),
            Call::create('getDenormalizationFieldMappings')->with('', 'object')->willReturn([
                $denormalizationNameFieldMapping,
                $denormalizationValueFieldMapping,
            ]),
        ]);

        /** @var DenormalizerObjectMappingRegistryInterface|MockObject $registry */
        $registry = $this->getMockByCalls(DenormalizerObjectMappingRegistryInterface::class, [
            Call::create('getObjectMapping')->with(\stdClass::class)->willReturn($objectMapping),
        ]);

        /** @var LoggerInterface|MockObject $logger */
        $logger = $this->getMockByCalls(LoggerInterface::class, [
            Call::create('info')->with('deserialize: path {path}', ['path' => 'name']),
        ]);

        $denormalizer = new Denormalizer($registry, $logger);

        self::assertSame(
            $object,
            $denormalizer->denormalize(\stdClass::class, ['name' => 'name', '_type' => 'object'], $context)
        );
    }

    public function testDenormalizeWithNewAndTypeAndResetMissingFields()
    {
        $object = new \stdClass();

        $factory = function () use ($object) {
            return $object;
        };

        $context = DenormalizerContextBuilder::create()->setResetMissingFields(true)->getContext();

        /** @var FieldDenormalizerInterface|MockObject $nameFieldDenormalizer */
        $nameFieldDenormalizer = $this->getMockByCalls(FieldDenormalizerInterface::class, [
            Call::create('denormalizeField')
                ->with('name', $object, 'name', $context, new ArgumentInstanceOf(DenormalizerInterface::class)),
        ]);

        /** @var DenormalizationFieldMappingInterface|MockObject $denormalizationNameFieldMapping */
        $denormalizationNameFieldMapping = $this->getMockByCalls(DenormalizationFieldMappingInterface::class, [
            Call::create('getName')->with()->willReturn('name'),
            Call::create('getFieldDenormalizer')->with()->willReturn($nameFieldDenormalizer),
        ]);

        /** @var DenormalizationFieldMappingInterface|MockObject $denormalizationValueFieldMapping */
        $denormalizationValueFieldMapping = $this->getMockByCalls(DenormalizationFieldMappingInterface::class, [
            Call::create('getName')->with()->willReturn('value'),
        ]);

        /** @var DenormalizationObjectMappingInterface|MockObject $objectMapping */
        $objectMapping = $this->getMockByCalls(DenormalizationObjectMappingInterface::class, [
            Call::create('getDenormalizationFactory')->with('', 'object')->willReturn($factory),
            Call::create('getDenormalizationFieldMappings')->with('', 'object')->willReturn([
                $denormalizationNameFieldMapping,
                $denormalizationValueFieldMapping,
            ]),
        ]);

        /** @var DenormalizerObjectMappingRegistryInterface|MockObject $registry */
        $registry = $this->getMockByCalls(DenormalizerObjectMappingRegistryInterface::class, [
            Call::create('getObjectMapping')->with(\stdClass::class)->willReturn($objectMapping),
        ]);

        /** @var LoggerInterface|MockObject $logger */
        $logger = $this->getMockByCalls(LoggerInterface::class, [
            Call::create('info')->with('deserialize: path {path}', ['path' => 'name']),
        ]);

        $denormalizer = new Denormalizer($registry, $logger);

        self::assertSame(
            $object,
            $denormalizer->denormalize(\stdClass::class, ['name' => 'name', '_type' => 'object'], $context)
        );
    }

    public function testDenormalizeWithExisting()
    {
        $object = new \stdClass();

        /** @var DenormalizerContextInterface|MockObject $context */
        $context = $this->getMockByCalls(DenormalizerContextInterface::class, [
            Call::create('getGroups')->with()->willReturn([]),
            Call::create('getAllowedAdditionalFields')->with()->willReturn(null),
        ]);

        /** @var FieldDenormalizerInterface|MockObject $nameFieldDenormalizer */
        $nameFieldDenormalizer = $this->getMockByCalls(FieldDenormalizerInterface::class, [
            Call::create('denormalizeField')
                ->with('name', $object, 'name', $context, new ArgumentInstanceOf(DenormalizerInterface::class)),
        ]);

        /** @var DenormalizationFieldMappingInterface|MockObject $denormalizationNameFieldMapping */
        $denormalizationNameFieldMapping = $this->getMockByCalls(DenormalizationFieldMappingInterface::class, [
            Call::create('getName')->with()->willReturn('name'),
            Call::create('getFieldDenormalizer')->with()->willReturn($nameFieldDenormalizer),
        ]);

        /** @var DenormalizationFieldMappingInterface|MockObject $denormalizationValueFieldMapping */
        $denormalizationValueFieldMapping = $this->getMockByCalls(DenormalizationFieldMappingInterface::class, [
            Call::create('getName')->with()->willReturn('value'),
        ]);

        /** @var DenormalizationObjectMappingInterface|MockObject $objectMapping */
        $objectMapping = $this->getMockByCalls(DenormalizationObjectMappingInterface::class, [
            Call::create('getDenormalizationFieldMappings')->with('', null)->willReturn([
                $denormalizationNameFieldMapping,
                $denormalizationValueFieldMapping,
            ]),
        ]);

        /** @var DenormalizerObjectMappingRegistryInterface|MockObject $registry */
        $registry = $this->getMockByCalls(DenormalizerObjectMappingRegistryInterface::class, [
            Call::create('getObjectMapping')->with(\stdClass::class)->willReturn($objectMapping),
        ]);

        /** @var LoggerInterface|MockObject $logger */
        $logger = $this->getMockByCalls(LoggerInterface::class, [
            Call::create('info')->with('deserialize: path {path}', ['path' => 'name']),
        ]);

        $denormalizer = new Denormalizer($registry, $logger);

        self::assertSame($object, $denormalizer->denormalize($object, ['name' => 'name'], $context));
    }

    public function testDenormalizeWithMoreThanOneDefinitionForOneField()
    {
        $object = new \stdClass();

        /** @var DenormalizerContextInterface|MockObject $context */
        $context = $this->getMockByCalls(DenormalizerContextInterface::class, [
            Call::create('getGroups')->with()->willReturn([]),
            Call::create('getGroups')->with()->willReturn([]),
            Call::create('getAllowedAdditionalFields')->with()->willReturn([]),
        ]);

        /** @var FieldDenormalizerInterface|MockObject $nameFieldDenormalizer */
        $nameFieldDenormalizer = $this->getMockByCalls(FieldDenormalizerInterface::class, [
            Call::create('denormalizeField')
                ->with('name', $object, 'name', $context, new ArgumentInstanceOf(DenormalizerInterface::class)),
        ]);

        /** @var DenormalizationFieldMappingInterface|MockObject $denormalizationNameFieldMapping */
        $denormalizationNameFieldMapping = $this->getMockByCalls(DenormalizationFieldMappingInterface::class, [
            Call::create('getName')->with()->willReturn('name'),
            Call::create('getFieldDenormalizer')->with()->willReturn($nameFieldDenormalizer),
        ]);

        /** @var FieldDenormalizerInterface|MockObject $nameFieldDenormalizer */
        $nameFieldDenormalizer2 = $this->getMockByCalls(FieldDenormalizerInterface::class, [
            Call::create('denormalizeField')
                ->with('name', $object, 'name', $context, new ArgumentInstanceOf(DenormalizerInterface::class)),
        ]);

        /** @var DenormalizationFieldMappingInterface|MockObject $denormalizationNameFieldMapping */
        $denormalizationNameFieldMapping2 = $this->getMockByCalls(DenormalizationFieldMappingInterface::class, [
            Call::create('getName')->with()->willReturn('name'),
            Call::create('getFieldDenormalizer')->with()->willReturn($nameFieldDenormalizer2),
        ]);

        /** @var DenormalizationObjectMappingInterface|MockObject $objectMapping */
        $objectMapping = $this->getMockByCalls(DenormalizationObjectMappingInterface::class, [
            Call::create('getDenormalizationFieldMappings')->with('', null)->willReturn([
                $denormalizationNameFieldMapping,
                $denormalizationNameFieldMapping2,
            ]),
        ]);

        /** @var DenormalizerObjectMappingRegistryInterface|MockObject $registry */
        $registry = $this->getMockByCalls(DenormalizerObjectMappingRegistryInterface::class, [
            Call::create('getObjectMapping')->with(\stdClass::class)->willReturn($objectMapping),
        ]);

        /** @var LoggerInterface|MockObject $logger */
        $logger = $this->getMockByCalls(LoggerInterface::class, [
            Call::create('info')->with('deserialize: path {path}', ['path' => 'name']),
            Call::create('info')->with('deserialize: path {path}', ['path' => 'name']),
        ]);

        $denormalizer = new Denormalizer($registry, $logger);

        self::assertSame($object, $denormalizer->denormalize($object, ['name' => 'name'], $context));
    }

    public function testDenormalizeWithExistingAndResetMissingFields()
    {
        $object = new class() {
            public $value;
        };

        $factory = function () {
            return new class() {
                public $value = 'initialValue';
            };
        };

        $context = DenormalizerContextBuilder::create()->setResetMissingFields(true)->getContext();

        /** @var FieldDenormalizerInterface|MockObject $nameFieldDenormalizer */
        $nameFieldDenormalizer = $this->getMockByCalls(FieldDenormalizerInterface::class, [
            Call::create('denormalizeField')
                ->with('name', $object, 'name', $context, new ArgumentInstanceOf(DenormalizerInterface::class)),
        ]);

        /** @var DenormalizationFieldMappingInterface|MockObject $denormalizationNameFieldMapping */
        $denormalizationNameFieldMapping = $this->getMockByCalls(DenormalizationFieldMappingInterface::class, [
            Call::create('getName')->with()->willReturn('name'),
            Call::create('getFieldDenormalizer')->with()->willReturn($nameFieldDenormalizer),
        ]);

        /** @var DenormalizationFieldMappingInterface|MockObject $denormalizationValueFieldMapping */
        $denormalizationValueFieldMapping = $this->getMockByCalls(DenormalizationFieldMappingInterface::class, [
            Call::create('getName')->with()->willReturn('value'),
        ]);

        /** @var DenormalizationObjectMappingInterface|MockObject $objectMapping */
        $objectMapping = $this->getMockByCalls(DenormalizationObjectMappingInterface::class, [
            Call::create('getDenormalizationFieldMappings')->with('', null)->willReturn([
                $denormalizationNameFieldMapping,
                $denormalizationValueFieldMapping,
            ]),
            Call::create('getDenormalizationFactory')->with('', null)->willReturn($factory),
        ]);

        /** @var DenormalizerObjectMappingRegistryInterface|MockObject $registry */
        $registry = $this->getMockByCalls(DenormalizerObjectMappingRegistryInterface::class, [
            Call::create('getObjectMapping')->with(get_class($object))->willReturn($objectMapping),
        ]);

        /** @var LoggerInterface|MockObject $logger */
        $logger = $this->getMockByCalls(LoggerInterface::class, [
            Call::create('info')->with('deserialize: path {path}', ['path' => 'name']),
        ]);

        $denormalizer = new Denormalizer($registry, $logger);

        self::assertSame($object, $denormalizer->denormalize($object, ['name' => 'name'], $context));

        self::assertSame('initialValue', $object->value);
    }

    public function testDenormalizeWithNotWorkingFactory()
    {
        $this->expectException(DeserializerLogicException::class);
        $this->expectExceptionMessage('Factory does not return object, "string" given at path: ""');

        $factory = function () {
            return 'string';
        };

        /** @var DenormalizerContextInterface|MockObject $context */
        $context = $this->getMockByCalls(DenormalizerContextInterface::class);

        /** @var DenormalizationObjectMappingInterface|MockObject $objectMapping */
        $objectMapping = $this->getMockByCalls(DenormalizationObjectMappingInterface::class, [
            Call::create('getDenormalizationFactory')->with('', null)->willReturn($factory),
        ]);

        /** @var DenormalizerObjectMappingRegistryInterface|MockObject $registry */
        $registry = $this->getMockByCalls(DenormalizerObjectMappingRegistryInterface::class, [
            Call::create('getObjectMapping')->with(\stdClass::class)->willReturn($objectMapping),
        ]);

        /** @var LoggerInterface|MockObject $logger */
        $logger = $this->getMockByCalls(LoggerInterface::class, [
            Call::create('error')
                ->with('deserialize: {exception}', [
                    'exception' => 'Factory does not return object, "string" given at path: ""',
                ]),
        ]);

        $denormalizer = new Denormalizer($registry, $logger);
        $denormalizer->denormalize(\stdClass::class, ['name' => 'name'], $context);
    }

    public function testDenormalizeWithAdditionalData()
    {
        $this->expectException(DeserializerRuntimeException::class);
        $this->expectExceptionMessage('There are additional field(s) at paths: "additionalData"');

        $object = new \stdClass();

        $factory = function () use ($object) {
            return $object;
        };

        /** @var DenormalizerContextInterface|MockObject $context */
        $context = $this->getMockByCalls(DenormalizerContextInterface::class, [
            Call::create('getAllowedAdditionalFields')->with()->willReturn([]),
        ]);

        /** @var DenormalizationFieldMappingInterface|MockObject $denormalizationNameFieldMapping */
        $denormalizationNameFieldMapping = $this->getMockByCalls(DenormalizationFieldMappingInterface::class, [
            Call::create('getName')->with()->willReturn('name'),
        ]);

        /** @var DenormalizationFieldMappingInterface|MockObject $denormalizationValueFieldMapping */
        $denormalizationValueFieldMapping = $this->getMockByCalls(DenormalizationFieldMappingInterface::class, [
            Call::create('getName')->with()->willReturn('value'),
        ]);

        /** @var DenormalizationObjectMappingInterface|MockObject $objectMapping */
        $objectMapping = $this->getMockByCalls(DenormalizationObjectMappingInterface::class, [
            Call::create('getDenormalizationFactory')->with('', null)->willReturn($factory),
            Call::create('getDenormalizationFieldMappings')->with('', null)->willReturn([
                $denormalizationNameFieldMapping,
                $denormalizationValueFieldMapping,
            ]),
        ]);

        /** @var DenormalizerObjectMappingRegistryInterface|MockObject $registry */
        $registry = $this->getMockByCalls(DenormalizerObjectMappingRegistryInterface::class, [
            Call::create('getObjectMapping')->with(\stdClass::class)->willReturn($objectMapping),
        ]);

        /** @var LoggerInterface|MockObject $logger */
        $logger = $this->getMockByCalls(LoggerInterface::class, [
            Call::create('notice')->with('deserialize: {exception}', [
                'exception' => 'There are additional field(s) at paths: "additionalData"',
            ]),
        ]);

        $denormalizer = new Denormalizer($registry, $logger);
        $denormalizer->denormalize(\stdClass::class, ['additionalData' => 'additionalData'], $context);
    }

    public function testDenormalizeWithAdditionalDataAndAllowIt()
    {
        $object = new \stdClass();

        $factory = function () use ($object) {
            return $object;
        };

        /** @var DenormalizerContextInterface|MockObject $context */
        $context = $this->getMockByCalls(DenormalizerContextInterface::class, [
            Call::create('getAllowedAdditionalFields')->with()->willReturn(null),
        ]);

        /** @var DenormalizationFieldMappingInterface|MockObject $denormalizationNameFieldMapping */
        $denormalizationNameFieldMapping = $this->getMockByCalls(DenormalizationFieldMappingInterface::class, [
            Call::create('getName')->with()->willReturn('name'),
        ]);

        /** @var DenormalizationFieldMappingInterface|MockObject $denormalizationValueFieldMapping */
        $denormalizationValueFieldMapping = $this->getMockByCalls(DenormalizationFieldMappingInterface::class, [
            Call::create('getName')->with()->willReturn('value'),
        ]);

        /** @var DenormalizationObjectMappingInterface|MockObject $objectMapping */
        $objectMapping = $this->getMockByCalls(DenormalizationObjectMappingInterface::class, [
            Call::create('getDenormalizationFactory')->with('', null)->willReturn($factory),
            Call::create('getDenormalizationFieldMappings')->with('', null)->willReturn([
                $denormalizationNameFieldMapping,
                $denormalizationValueFieldMapping,
            ]),
        ]);

        /** @var DenormalizerObjectMappingRegistryInterface|MockObject $registry */
        $registry = $this->getMockByCalls(DenormalizerObjectMappingRegistryInterface::class, [
            Call::create('getObjectMapping')->with(\stdClass::class)->willReturn($objectMapping),
        ]);

        /** @var LoggerInterface|MockObject $logger */
        $logger = $this->getMockByCalls(LoggerInterface::class);

        $denormalizer = new Denormalizer($registry, $logger);
        $denormalizer->denormalize(\stdClass::class, ['additionalData' => 'additionalData'], $context);
    }

    public function testDenormalizeWithMissingObjectMapping()
    {
        $this->expectException(DeserializerLogicException::class);
        $this->expectExceptionMessage('There is no mapping for class: "stdClass"');

        $object = new \stdClass();

        $exception = DeserializerLogicException::createMissingMapping(\stdClass::class);

        /** @var DenormalizerContextInterface|MockObject $context */
        $context = $this->getMockByCalls(DenormalizerContextInterface::class);

        /** @var DenormalizerObjectMappingRegistryInterface|MockObject $registry */
        $registry = $this->getMockByCalls(DenormalizerObjectMappingRegistryInterface::class, [
            Call::create('getObjectMapping')->with(\stdClass::class)->willThrowException($exception),
        ]);

        /** @var LoggerInterface|MockObject $logger */
        $logger = $this->getMockByCalls(LoggerInterface::class, [
            Call::create('error')->with('deserialize: {exception}', [
                'exception' => 'There is no mapping for class: "stdClass"',
            ]),
        ]);

        $denormalizer = new Denormalizer($registry, $logger);
        $denormalizer->denormalize(\stdClass::class, ['name' => 'name'], $context);
    }

    public function testDenormalizeWithGroups()
    {
        $object = new \stdClass();

        $factory = function () use ($object) {
            return $object;
        };

        /** @var DenormalizerContextInterface|MockObject $context */
        $context = $this->getMockByCalls(DenormalizerContextInterface::class, [
            Call::create('getGroups')->with()->willReturn(['read']),
            Call::create('getAllowedAdditionalFields')->with()->willReturn(null),
        ]);

        /** @var FieldDenormalizerInterface|MockObject $nameFieldDenormalizer */
        $nameFieldDenormalizer = $this->getMockByCalls(FieldDenormalizerInterface::class, [
            Call::create('denormalizeField')
                ->with('name', $object, 'name', $context, new ArgumentInstanceOf(DenormalizerInterface::class)),
        ]);

        /** @var DenormalizationFieldMappingInterface|MockObject $denormalizationNameFieldMapping */
        $denormalizationNameFieldMapping = $this->getMockByCalls(DenormalizationFieldMappingInterface::class, [
            Call::create('getName')->with()->willReturn('name'),
            Call::create('getGroups')->with()->willReturn(['read']),
            Call::create('getFieldDenormalizer')->with()->willReturn($nameFieldDenormalizer),
        ]);

        /** @var DenormalizationFieldMappingInterface|MockObject $denormalizationValueFieldMapping */
        $denormalizationValueFieldMapping = $this->getMockByCalls(DenormalizationFieldMappingInterface::class, [
            Call::create('getName')->with()->willReturn('value'),
        ]);

        /** @var DenormalizationObjectMappingInterface|MockObject $objectMapping */
        $objectMapping = $this->getMockByCalls(DenormalizationObjectMappingInterface::class, [
            Call::create('getDenormalizationFactory')->with('', null)->willReturn($factory),
            Call::create('getDenormalizationFieldMappings')->with('', null)->willReturn([
                $denormalizationNameFieldMapping,
                $denormalizationValueFieldMapping,
            ]),
        ]);

        /** @var DenormalizerObjectMappingRegistryInterface|MockObject $registry */
        $registry = $this->getMockByCalls(DenormalizerObjectMappingRegistryInterface::class, [
            Call::create('getObjectMapping')->with(\stdClass::class)->willReturn($objectMapping),
        ]);

        /** @var LoggerInterface|MockObject $logger */
        $logger = $this->getMockByCalls(LoggerInterface::class, [
            Call::create('info')->with('deserialize: path {path}', ['path' => 'name']),
        ]);

        $denormalizer = new Denormalizer($registry, $logger);

        self::assertSame($object, $denormalizer->denormalize(\stdClass::class, ['name' => 'name'], $context));
    }

    public function testDenormalizeWithGroupsButNoGroupOnField()
    {
        $object = new \stdClass();

        $factory = function () use ($object) {
            return $object;
        };

        /** @var DenormalizerContextInterface|MockObject $context */
        $context = $this->getMockByCalls(DenormalizerContextInterface::class, [
            Call::create('getGroups')->with()->willReturn(['read']),
            Call::create('getAllowedAdditionalFields')->with()->willReturn(null),
        ]);

        /** @var DenormalizationFieldMappingInterface|MockObject $denormalizationNameFieldMapping */
        $denormalizationNameFieldMapping = $this->getMockByCalls(DenormalizationFieldMappingInterface::class, [
            Call::create('getName')->with()->willReturn('name'),
            Call::create('getGroups')->with()->willReturn([]),
        ]);

        /** @var DenormalizationFieldMappingInterface|MockObject $denormalizationValueFieldMapping */
        $denormalizationValueFieldMapping = $this->getMockByCalls(DenormalizationFieldMappingInterface::class, [
            Call::create('getName')->with()->willReturn('value'),
        ]);

        /** @var DenormalizationObjectMappingInterface|MockObject $objectMapping */
        $objectMapping = $this->getMockByCalls(DenormalizationObjectMappingInterface::class, [
            Call::create('getDenormalizationFactory')->with('', null)->willReturn($factory),
            Call::create('getDenormalizationFieldMappings')->with('', null)->willReturn([
                $denormalizationNameFieldMapping,
                $denormalizationValueFieldMapping,
            ]),
        ]);

        /** @var DenormalizerObjectMappingRegistryInterface|MockObject $registry */
        $registry = $this->getMockByCalls(DenormalizerObjectMappingRegistryInterface::class, [
            Call::create('getObjectMapping')->with(\stdClass::class)->willReturn($objectMapping),
        ]);

        /** @var LoggerInterface|MockObject $logger */
        $logger = $this->getMockByCalls(LoggerInterface::class);

        $denormalizer = new Denormalizer($registry, $logger);

        self::assertSame($object, $denormalizer->denormalize(\stdClass::class, ['name' => 'name'], $context));
    }

    public function testDenormalizeWithCompliantPolicy()
    {
        $object = new \stdClass();

        $factory = function () use ($object) {
            return $object;
        };

        /** @var DenormalizerContextInterface|MockObject $context */
        $context = $this->getMockByCalls(DenormalizerContextInterface::class, [
            Call::create('getGroups')->with()->willReturn([]),
            Call::create('getAllowedAdditionalFields')->with()->willReturn(null),
        ]);

        /** @var FieldDenormalizerInterface|MockObject $nameFieldDenormalizer */
        $nameFieldDenormalizer = $this->getMockByCalls(FieldDenormalizerInterface::class, [
            Call::create('denormalizeField')
                ->with('name', $object, 'name', $context, new ArgumentInstanceOf(DenormalizerInterface::class)),
        ]);

        /** @var PolicyInterface|MockObject $policy */
        $policy = $this->getMockByCalls(PolicyInterface::class, [
            Call::create('isCompliant')->with($context, $object)->willReturn(true),
        ]);

        $denormalizationNameFieldMapping = $this->getDenormalizationFieldMappingWithPolicy(
            'name',
            $nameFieldDenormalizer,
            $policy
        );

        /** @var DenormalizationFieldMappingInterface|MockObject $denormalizationValueFieldMapping */
        $denormalizationValueFieldMapping = $this->getMockByCalls(DenormalizationFieldMappingInterface::class, [
            Call::create('getName')->with()->willReturn('value'),
        ]);

        /** @var DenormalizationObjectMappingInterface|MockObject $objectMapping */
        $objectMapping = $this->getMockByCalls(DenormalizationObjectMappingInterface::class, [
            Call::create('getDenormalizationFactory')->with('', null)->willReturn($factory),
            Call::create('getDenormalizationFieldMappings')->with('', null)->willReturn([
                $denormalizationNameFieldMapping,
                $denormalizationValueFieldMapping,
            ]),
        ]);

        /** @var DenormalizerObjectMappingRegistryInterface|MockObject $registry */
        $registry = $this->getMockByCalls(DenormalizerObjectMappingRegistryInterface::class, [
            Call::create('getObjectMapping')->with(\stdClass::class)->willReturn($objectMapping),
        ]);

        /** @var LoggerInterface|MockObject $logger */
        $logger = $this->getMockByCalls(LoggerInterface::class, [
            Call::create('info')->with('deserialize: path {path}', ['path' => 'name']),
        ]);

        $denormalizer = new Denormalizer($registry, $logger);

        self::assertSame($object, $denormalizer->denormalize(\stdClass::class, ['name' => 'name'], $context));
    }

    public function testDenormalizeWithNotCompliantPolicy()
    {
        $object = new \stdClass();

        $factory = function () use ($object) {
            return $object;
        };

        /** @var DenormalizerContextInterface|MockObject $context */
        $context = $this->getMockByCalls(DenormalizerContextInterface::class, [
            Call::create('getAllowedAdditionalFields')->with()->willReturn(null),
        ]);

        /** @var FieldDenormalizerInterface|MockObject $nameFieldDenormalizer */
        $nameFieldDenormalizer = $this->getMockByCalls(FieldDenormalizerInterface::class);

        /** @var PolicyInterface|MockObject $policy */
        $policy = $this->getMockByCalls(PolicyInterface::class, [
            Call::create('isCompliant')->with($context, $object)->willReturn(false),
        ]);

        $denormalizationNameFieldMapping = $this->getDenormalizationFieldMappingWithPolicy(
            'name',
            $nameFieldDenormalizer,
            $policy
        );

        /** @var DenormalizationFieldMappingInterface|MockObject $denormalizationValueFieldMapping */
        $denormalizationValueFieldMapping = $this->getMockByCalls(DenormalizationFieldMappingInterface::class, [
            Call::create('getName')->with()->willReturn('value'),
        ]);

        /** @var DenormalizationObjectMappingInterface|MockObject $objectMapping */
        $objectMapping = $this->getMockByCalls(DenormalizationObjectMappingInterface::class, [
            Call::create('getDenormalizationFactory')->with('', null)->willReturn($factory),
            Call::create('getDenormalizationFieldMappings')->with('', null)->willReturn([
                $denormalizationNameFieldMapping,
                $denormalizationValueFieldMapping,
            ]),
        ]);

        /** @var DenormalizerObjectMappingRegistryInterface|MockObject $registry */
        $registry = $this->getMockByCalls(DenormalizerObjectMappingRegistryInterface::class, [
            Call::create('getObjectMapping')->with(\stdClass::class)->willReturn($objectMapping),
        ]);

        /** @var LoggerInterface|MockObject $logger */
        $logger = $this->getMockByCalls(LoggerInterface::class);

        $denormalizer = new Denormalizer($registry, $logger);

        self::assertSame($object, $denormalizer->denormalize(\stdClass::class, ['name' => 'name'], $context));
    }

    /**
     * @param string                     $name
     * @param FieldDenormalizerInterface $fieldDenormalizer
     * @param PolicyInterface            $policy
     * @param array                      $groups
     *
     * @return DenormalizationFieldMappingInterface
     *
     * @todo remove as soon getPolicy() is part of the mapping interface
     */
    private function getDenormalizationFieldMappingWithPolicy(
        string $name,
        FieldDenormalizerInterface $fieldDenormalizer,
        PolicyInterface $policy,
        array $groups = []
    ): DenormalizationFieldMappingInterface {
        return new class($name, $fieldDenormalizer, $policy, $groups) implements DenormalizationFieldMappingInterface {
            private $name;
            private $fieldDenormalizer;
            private $policy;
            private $groups;

            public function __construct($name, $fieldDenormalizer, $policy, $groups)
            {
                $this->name = $name;
                $this->fieldDenormalizer = $fieldDenormalizer;
                $this->policy = $policy;
                $this->groups = $groups;
            }

            public function getName(): string
            {
                return $this->name;
            }

            /**
             * @return string[]
             *
             * @deprecated
             */
            public function getGroups(): array
            {
                return $this->groups;
            }

            /**
             * @return FieldDenormalizerInterface
             */
            public function getFieldDenormalizer(): FieldDenormalizerInterface
            {
                return $this->fieldDenormalizer;
            }

            public function getPolicy(): PolicyInterface
            {
                return $this->policy;
            }
        };
    }
}
