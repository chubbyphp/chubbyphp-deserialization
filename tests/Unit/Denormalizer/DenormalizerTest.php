<?php

declare(strict_types=1);

namespace Chubbyphp\Tests\Deserialization\Unit\Denormalizer;

use Chubbyphp\Deserialization\Denormalizer\Denormalizer;
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
final class DenormalizerTest extends TestCase
{
    use MockByCallsTrait;

    public function testDenormalizeWithNew(): void
    {
        $object = new \stdClass();

        $factory = function () use ($object) {
            return $object;
        };

        /** @var DenormalizerContextInterface|MockObject $context */
        $context = $this->getMockByCalls(DenormalizerContextInterface::class, [
            Call::create('isClearMissing')->with()->willReturn(false),
            Call::create('getAllowedAdditionalFields')->with()->willReturn(null),
        ]);

        /** @var PolicyInterface|MockObject $namePolicy */
        $namePolicy = $this->getMockByCalls(PolicyInterface::class, [
            Call::create('isCompliantIncludingPath')->with('name', $object, $context)->willReturn(true),
        ]);

        /** @var FieldDenormalizerInterface|MockObject $nameFieldDenormalizer */
        $nameFieldDenormalizer = $this->getMockByCalls(FieldDenormalizerInterface::class, [
            Call::create('denormalizeField')
                ->with('name', $object, 'name', $context, new ArgumentInstanceOf(DenormalizerInterface::class)),
        ]);

        /** @var DenormalizationFieldMappingInterface|MockObject $denormalizationNameFieldMapping */
        $denormalizationNameFieldMapping = $this->getMockByCalls(DenormalizationFieldMappingInterface::class, [
            Call::create('getName')->with()->willReturn('name'),
            Call::create('getPolicy')->with()->willReturn($namePolicy),
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

    public function testDenormalizeWithNewAndType(): void
    {
        $object = new \stdClass();

        $factory = function () use ($object) {
            return $object;
        };

        /** @var DenormalizerContextInterface|MockObject $context */
        $context = $this->getMockByCalls(DenormalizerContextInterface::class, [
            Call::create('isClearMissing')->with()->willReturn(false),
            Call::create('getAllowedAdditionalFields')->with()->willReturn(null),
        ]);

        /** @var PolicyInterface|MockObject $namePolicy */
        $namePolicy = $this->getMockByCalls(PolicyInterface::class, [
            Call::create('isCompliantIncludingPath')->with('name', $object, $context)->willReturn(true),
        ]);

        /** @var FieldDenormalizerInterface|MockObject $nameFieldDenormalizer */
        $nameFieldDenormalizer = $this->getMockByCalls(FieldDenormalizerInterface::class, [
            Call::create('denormalizeField')
                ->with('name', $object, 'name', $context, new ArgumentInstanceOf(DenormalizerInterface::class)),
        ]);

        /** @var DenormalizationFieldMappingInterface|MockObject $denormalizationNameFieldMapping */
        $denormalizationNameFieldMapping = $this->getMockByCalls(DenormalizationFieldMappingInterface::class, [
            Call::create('getName')->with()->willReturn('name'),
            Call::create('getPolicy')->with()->willReturn($namePolicy),
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

    public function testDenormalizeWithExisting(): void
    {
        $object = new \stdClass();

        /** @var DenormalizerContextInterface|MockObject $context */
        $context = $this->getMockByCalls(DenormalizerContextInterface::class, [
            Call::create('isClearMissing')->with()->willReturn(false),
            Call::create('getAllowedAdditionalFields')->with()->willReturn(null),
        ]);

        /** @var PolicyInterface|MockObject $namePolicy */
        $namePolicy = $this->getMockByCalls(PolicyInterface::class, [
            Call::create('isCompliantIncludingPath')->with('name', $object, $context)->willReturn(true),
        ]);

        /** @var FieldDenormalizerInterface|MockObject $nameFieldDenormalizer */
        $nameFieldDenormalizer = $this->getMockByCalls(FieldDenormalizerInterface::class, [
            Call::create('denormalizeField')
                ->with('name', $object, 'name', $context, new ArgumentInstanceOf(DenormalizerInterface::class)),
        ]);

        /** @var DenormalizationFieldMappingInterface|MockObject $denormalizationNameFieldMapping */
        $denormalizationNameFieldMapping = $this->getMockByCalls(DenormalizationFieldMappingInterface::class, [
            Call::create('getName')->with()->willReturn('name'),
            Call::create('getPolicy')->with()->willReturn($namePolicy),
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

    public function testDenormalizeWithMoreThanOneDefinitionForOneField(): void
    {
        $object = new \stdClass();

        /** @var DenormalizerContextInterface|MockObject $context */
        $context = $this->getMockByCalls(DenormalizerContextInterface::class, [
            Call::create('getAllowedAdditionalFields')->with()->willReturn(null),
        ]);

        /** @var PolicyInterface|MockObject $namePolicy1 */
        $namePolicy1 = $this->getMockByCalls(PolicyInterface::class, [
            Call::create('isCompliantIncludingPath')->with('name', $object, $context)->willReturn(true),
        ]);

        /** @var FieldDenormalizerInterface|MockObject $nameFieldDenormalizer1 */
        $nameFieldDenormalizer1 = $this->getMockByCalls(FieldDenormalizerInterface::class, [
            Call::create('denormalizeField')
                ->with('name', $object, 'name', $context, new ArgumentInstanceOf(DenormalizerInterface::class)),
        ]);

        /** @var DenormalizationFieldMappingInterface|MockObject $denormalizationNameFieldMapping */
        $denormalizationNameFieldMapping1 = $this->getMockByCalls(DenormalizationFieldMappingInterface::class, [
            Call::create('getName')->with()->willReturn('name'),
            Call::create('getPolicy')->with()->willReturn($namePolicy1),
            Call::create('getFieldDenormalizer')->with()->willReturn($nameFieldDenormalizer1),
        ]);

        /** @var PolicyInterface|MockObject $namePolicy2 */
        $namePolicy2 = $this->getMockByCalls(PolicyInterface::class, [
            Call::create('isCompliantIncludingPath')->with('name', $object, $context)->willReturn(true),
        ]);

        /** @var FieldDenormalizerInterface|MockObject $nameFieldDenormalizer */
        $nameFieldDenormalizer2 = $this->getMockByCalls(FieldDenormalizerInterface::class, [
            Call::create('denormalizeField')
                ->with('name', $object, 'name', $context, new ArgumentInstanceOf(DenormalizerInterface::class)),
        ]);

        /** @var DenormalizationFieldMappingInterface|MockObject $denormalizationNameFieldMapping */
        $denormalizationNameFieldMapping2 = $this->getMockByCalls(DenormalizationFieldMappingInterface::class, [
            Call::create('getName')->with()->willReturn('name'),
            Call::create('getPolicy')->with()->willReturn($namePolicy2),
            Call::create('getFieldDenormalizer')->with()->willReturn($nameFieldDenormalizer2),
        ]);

        /** @var DenormalizationObjectMappingInterface|MockObject $objectMapping */
        $objectMapping = $this->getMockByCalls(DenormalizationObjectMappingInterface::class, [
            Call::create('getDenormalizationFieldMappings')->with('', null)->willReturn([
                $denormalizationNameFieldMapping1,
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

    public function testIsCleanMissing(): void
    {
        $object = new \stdClass();

        $factory = function () use ($object) {
            return $object;
        };

        /** @var DenormalizerContextInterface|MockObject $context */
        $context = $this->getMockByCalls(DenormalizerContextInterface::class, [
            Call::create('isClearMissing')->with()->willReturn(true),
            Call::create('getAllowedAdditionalFields')->with()->willReturn(null),
        ]);

        /** @var PolicyInterface|MockObject $namePolicy */
        $namePolicy = $this->getMockByCalls(PolicyInterface::class, [
            Call::create('isCompliantIncludingPath')->with('name', $object, $context)->willReturn(true),
        ]);

        /** @var FieldDenormalizerInterface|MockObject $nameFieldDenormalizer */
        $nameFieldDenormalizer = $this->getMockByCalls(FieldDenormalizerInterface::class, [
            Call::create('denormalizeField')
                ->with('name', $object, 'name', $context, new ArgumentInstanceOf(DenormalizerInterface::class)),
        ]);

        /** @var DenormalizationFieldMappingInterface|MockObject $denormalizationNameFieldMapping */
        $denormalizationNameFieldMapping = $this->getMockByCalls(DenormalizationFieldMappingInterface::class, [
            Call::create('getName')->with()->willReturn('name'),
            Call::create('getPolicy')->with()->willReturn($namePolicy),
            Call::create('getFieldDenormalizer')->with()->willReturn($nameFieldDenormalizer),
        ]);

        /** @var PolicyInterface|MockObject $valuePolicy */
        $valuePolicy = $this->getMockByCalls(PolicyInterface::class, [
            Call::create('isCompliantIncludingPath')->with('value', $object, $context)->willReturn(true),
        ]);

        /** @var FieldDenormalizerInterface|MockObject $valueFieldDenormalizer */
        $valueFieldDenormalizer = $this->getMockByCalls(FieldDenormalizerInterface::class, [
            Call::create('denormalizeField')
                ->with('value', $object, null, $context, new ArgumentInstanceOf(DenormalizerInterface::class)),
        ]);

        /** @var DenormalizationFieldMappingInterface|MockObject $denormalizationValueFieldMapping */
        $denormalizationValueFieldMapping = $this->getMockByCalls(DenormalizationFieldMappingInterface::class, [
            Call::create('getName')->with()->willReturn('value'),
            Call::create('getPolicy')->with()->willReturn($valuePolicy),
            Call::create('getFieldDenormalizer')->with()->willReturn($valueFieldDenormalizer),
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
            Call::create('info')->with('deserialize: path {path}', ['path' => 'value']),
        ]);

        $denormalizer = new Denormalizer($registry, $logger);

        self::assertSame(
            $object,
            $denormalizer->denormalize(\stdClass::class, ['name' => 'name', '_type' => 'object'], $context)
        );
    }

    public function testDenormalizeWithNotWorkingFactory(): void
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

    public function testDenormalizeWithAdditionalData(): void
    {
        $this->expectException(DeserializerRuntimeException::class);
        $this->expectExceptionMessage('There are additional field(s) at paths: "additionalData"');

        $object = new \stdClass();

        $factory = function () use ($object) {
            return $object;
        };

        /** @var DenormalizerContextInterface|MockObject $context */
        $context = $this->getMockByCalls(DenormalizerContextInterface::class, [
            Call::create('isClearMissing')->with()->willReturn(false),
            Call::create('isClearMissing')->with()->willReturn(false),
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

    public function testDenormalizeWithKeyCastToIntegerAdditionalDataExpectsException(): void
    {
        $this->expectException(DeserializerRuntimeException::class);
        $this->expectExceptionMessage('There are additional field(s) at paths: "1"');

        $object = new \stdClass();

        $factory = function () use ($object) {
            return $object;
        };

        /** @var DenormalizerContextInterface|MockObject $context */
        $context = $this->getMockByCalls(DenormalizerContextInterface::class, [
            Call::create('isClearMissing')->with()->willReturn(false),
            Call::create('isClearMissing')->with()->willReturn(false),
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
                'exception' => 'There are additional field(s) at paths: "1"',
            ]),
        ]);

        $denormalizer = new Denormalizer($registry, $logger);
        $denormalizer->denormalize(\stdClass::class, ['1' => 'additionalData'], $context);
    }

    public function testDenormalizeWithAdditionalDataAndAllowIt(): void
    {
        $object = new \stdClass();

        $factory = function () use ($object) {
            return $object;
        };

        /** @var DenormalizerContextInterface|MockObject $context */
        $context = $this->getMockByCalls(DenormalizerContextInterface::class, [
            Call::create('isClearMissing')->with()->willReturn(false),
            Call::create('isClearMissing')->with()->willReturn(false),
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

    public function testDenormalizeWithMissingObjectMapping(): void
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

    public function testDenormalizeWithNotCompliantPolicy(): void
    {
        $object = new \stdClass();

        $factory = function () use ($object) {
            return $object;
        };

        /** @var DenormalizerContextInterface|MockObject $context */
        $context = $this->getMockByCalls(DenormalizerContextInterface::class, [
            Call::create('isClearMissing')->with()->willReturn(false),
            Call::create('getAllowedAdditionalFields')->with()->willReturn(null),
        ]);

        /** @var PolicyInterface|MockObject $namePolicy */
        $namePolicy = $this->getMockByCalls(PolicyInterface::class, [
            Call::create('isCompliantIncludingPath')->with('name', $object, $context)->willReturn(false),
        ]);

        /** @var DenormalizationFieldMappingInterface|MockObject $denormalizationNameFieldMapping */
        $denormalizationNameFieldMapping = $this->getMockByCalls(DenormalizationFieldMappingInterface::class, [
            Call::create('getName')->with()->willReturn('name'),
            Call::create('getPolicy')->with()->willReturn($namePolicy),
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

    public function testDenormalizeWithInvalidType(): void
    {
        $this->expectException(DeserializerRuntimeException::class);
        $this->expectExceptionMessage('Type is not a string, "array" given at path: ""');

        /** @var DenormalizerContextInterface|MockObject $context */
        $context = $this->getMockByCalls(DenormalizerContextInterface::class);

        /** @var DenormalizerObjectMappingRegistryInterface|MockObject $registry */
        $registry = $this->getMockByCalls(DenormalizerObjectMappingRegistryInterface::class);

        /** @var LoggerInterface|MockObject $logger */
        $logger = $this->getMockByCalls(LoggerInterface::class, [
            Call::create('error')->with('deserialize: {exception}', ['exception' => 'Type is not a string, "array" given at path: ""']),
        ]);

        $denormalizer = new Denormalizer($registry, $logger);
        $denormalizer->denormalize(\stdClass::class, ['_type' => ['key' => 'value']], $context);
    }
}
