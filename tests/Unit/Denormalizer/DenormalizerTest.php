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
use Chubbyphp\Mock\MockMethod\WithCallback;
use Chubbyphp\Mock\MockMethod\WithException;
use Chubbyphp\Mock\MockMethod\WithoutReturn;
use Chubbyphp\Mock\MockMethod\WithReturn;
use Chubbyphp\Mock\MockObjectBuilder;
use PHPUnit\Framework\Attributes\DoesNotPerformAssertions;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;

/**
 * @covers \Chubbyphp\Deserialization\Denormalizer\Denormalizer
 *
 * @internal
 */
final class DenormalizerTest extends TestCase
{
    public function testDenormalizeWithNew(): void
    {
        $object = new \stdClass();

        $factory = static fn () => $object;

        $builder = new MockObjectBuilder();

        /** @var DenormalizerContextInterface $context */
        $context = $builder->create(DenormalizerContextInterface::class, [
            new WithReturn('isClearMissing', [], false),
            new WithReturn('getAllowedAdditionalFields', [], null),
        ]);

        /** @var PolicyInterface $namePolicy */
        $namePolicy = $builder->create(PolicyInterface::class, [
            new WithReturn('isCompliant', ['name', $object, $context], true),
        ]);

        /** @var FieldDenormalizerInterface $nameFieldDenormalizer */
        $nameFieldDenormalizer = $builder->create(FieldDenormalizerInterface::class, [
            new WithCallback('denormalizeField', static function (
                string $givenPath,
                object $givenObject,
                mixed $givenValue,
                DenormalizerContextInterface $givenContext,
                ?DenormalizerInterface $givenDenormalizer = null
            ) use ($object, $context): void {
                self::assertSame('name', $givenPath);
                self::assertSame($object, $givenObject);
                self::assertSame('name', $givenValue);
                self::assertSame($context, $givenContext);
                self::assertInstanceOf(DenormalizerInterface::class, $givenDenormalizer);
            }),
        ]);

        /** @var DenormalizationFieldMappingInterface $denormalizationNameFieldMapping */
        $denormalizationNameFieldMapping = $builder->create(DenormalizationFieldMappingInterface::class, [
            new WithReturn('getName', [], 'name'),
            new WithReturn('getPolicy', [], $namePolicy),
            new WithReturn('getFieldDenormalizer', [], $nameFieldDenormalizer),
        ]);

        /** @var DenormalizationFieldMappingInterface $denormalizationValueFieldMapping */
        $denormalizationValueFieldMapping = $builder->create(DenormalizationFieldMappingInterface::class, [
            new WithReturn('getName', [], 'value'),
        ]);

        /** @var DenormalizationObjectMappingInterface $objectMapping */
        $objectMapping = $builder->create(DenormalizationObjectMappingInterface::class, [
            new WithReturn('getDenormalizationFactory', ['', null], $factory),
            new WithReturn('getDenormalizationFieldMappings', ['', null], [
                $denormalizationNameFieldMapping,
                $denormalizationValueFieldMapping,
            ]),
        ]);

        /** @var DenormalizerObjectMappingRegistryInterface $registry */
        $registry = $builder->create(DenormalizerObjectMappingRegistryInterface::class, [
            new WithReturn('getObjectMapping', [\stdClass::class], $objectMapping),
        ]);

        /** @var LoggerInterface $logger */
        $logger = $builder->create(LoggerInterface::class, [
            new WithoutReturn('info', ['deserialize: path {path}', ['path' => 'name']]),
        ]);

        $denormalizer = new Denormalizer($registry, $logger);

        self::assertSame($object, $denormalizer->denormalize(\stdClass::class, ['name' => 'name'], $context));
    }

    public function testDenormalizeWithNewAndType(): void
    {
        $object = new \stdClass();

        $factory = static fn () => $object;

        $builder = new MockObjectBuilder();

        /** @var DenormalizerContextInterface $context */
        $context = $builder->create(DenormalizerContextInterface::class, [
            new WithReturn('isClearMissing', [], false),
            new WithReturn('getAllowedAdditionalFields', [], null),
        ]);

        /** @var PolicyInterface $namePolicy */
        $namePolicy = $builder->create(PolicyInterface::class, [
            new WithReturn('isCompliant', ['name', $object, $context], true),
        ]);

        /** @var FieldDenormalizerInterface $nameFieldDenormalizer */
        $nameFieldDenormalizer = $builder->create(FieldDenormalizerInterface::class, [
            new WithCallback('denormalizeField', static function (
                string $givenPath,
                object $givenObject,
                mixed $givenValue,
                DenormalizerContextInterface $givenContext,
                ?DenormalizerInterface $givenDenormalizer = null
            ) use ($object, $context): void {
                self::assertSame('name', $givenPath);
                self::assertSame($object, $givenObject);
                self::assertSame('name', $givenValue);
                self::assertSame($context, $givenContext);
                self::assertInstanceOf(DenormalizerInterface::class, $givenDenormalizer);
            }),
        ]);

        /** @var DenormalizationFieldMappingInterface $denormalizationNameFieldMapping */
        $denormalizationNameFieldMapping = $builder->create(DenormalizationFieldMappingInterface::class, [
            new WithReturn('getName', [], 'name'),
            new WithReturn('getPolicy', [], $namePolicy),
            new WithReturn('getFieldDenormalizer', [], $nameFieldDenormalizer),
        ]);

        /** @var DenormalizationFieldMappingInterface $denormalizationValueFieldMapping */
        $denormalizationValueFieldMapping = $builder->create(DenormalizationFieldMappingInterface::class, [
            new WithReturn('getName', [], 'value'),
        ]);

        /** @var DenormalizationObjectMappingInterface $objectMapping */
        $objectMapping = $builder->create(DenormalizationObjectMappingInterface::class, [
            new WithReturn('getDenormalizationFactory', ['', 'object'], $factory),
            new WithReturn('getDenormalizationFieldMappings', ['', 'object'], [
                $denormalizationNameFieldMapping,
                $denormalizationValueFieldMapping,
            ]),
        ]);

        /** @var DenormalizerObjectMappingRegistryInterface $registry */
        $registry = $builder->create(DenormalizerObjectMappingRegistryInterface::class, [
            new WithReturn('getObjectMapping', [\stdClass::class], $objectMapping),
        ]);

        /** @var LoggerInterface $logger */
        $logger = $builder->create(LoggerInterface::class, [
            new WithoutReturn('info', ['deserialize: path {path}', ['path' => 'name']]),
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

        $builder = new MockObjectBuilder();

        /** @var DenormalizerContextInterface $context */
        $context = $builder->create(DenormalizerContextInterface::class, [
            new WithReturn('isClearMissing', [], false),
            new WithReturn('getAllowedAdditionalFields', [], null),
        ]);

        /** @var PolicyInterface $namePolicy */
        $namePolicy = $builder->create(PolicyInterface::class, [
            new WithReturn('isCompliant', ['name', $object, $context], true),
        ]);

        /** @var FieldDenormalizerInterface $nameFieldDenormalizer */
        $nameFieldDenormalizer = $builder->create(FieldDenormalizerInterface::class, [
            new WithCallback('denormalizeField', static function (
                string $givenPath,
                object $givenObject,
                mixed $givenValue,
                DenormalizerContextInterface $givenContext,
                ?DenormalizerInterface $givenDenormalizer = null
            ) use ($object, $context): void {
                self::assertSame('name', $givenPath);
                self::assertSame($object, $givenObject);
                self::assertSame('name', $givenValue);
                self::assertSame($context, $givenContext);
                self::assertInstanceOf(DenormalizerInterface::class, $givenDenormalizer);
            }),
        ]);

        /** @var DenormalizationFieldMappingInterface $denormalizationNameFieldMapping */
        $denormalizationNameFieldMapping = $builder->create(DenormalizationFieldMappingInterface::class, [
            new WithReturn('getName', [], 'name'),
            new WithReturn('getPolicy', [], $namePolicy),
            new WithReturn('getFieldDenormalizer', [], $nameFieldDenormalizer),
        ]);

        /** @var DenormalizationFieldMappingInterface $denormalizationValueFieldMapping */
        $denormalizationValueFieldMapping = $builder->create(DenormalizationFieldMappingInterface::class, [
            new WithReturn('getName', [], 'value'),
        ]);

        /** @var DenormalizationObjectMappingInterface $objectMapping */
        $objectMapping = $builder->create(DenormalizationObjectMappingInterface::class, [
            new WithReturn('getDenormalizationFieldMappings', ['', null], [
                $denormalizationNameFieldMapping,
                $denormalizationValueFieldMapping,
            ]),
        ]);

        /** @var DenormalizerObjectMappingRegistryInterface $registry */
        $registry = $builder->create(DenormalizerObjectMappingRegistryInterface::class, [
            new WithReturn('getObjectMapping', [\stdClass::class], $objectMapping),
        ]);

        /** @var LoggerInterface $logger */
        $logger = $builder->create(LoggerInterface::class, [
            new WithoutReturn('info', ['deserialize: path {path}', ['path' => 'name']]),
        ]);

        $denormalizer = new Denormalizer($registry, $logger);

        self::assertSame($object, $denormalizer->denormalize($object, ['name' => 'name'], $context));
    }

    public function testDenormalizeWithMoreThanOneDefinitionForOneField(): void
    {
        $object = new \stdClass();

        $builder = new MockObjectBuilder();

        /** @var DenormalizerContextInterface $context */
        $context = $builder->create(DenormalizerContextInterface::class, [
            new WithReturn('getAllowedAdditionalFields', [], null),
        ]);

        /** @var PolicyInterface $namePolicy1 */
        $namePolicy1 = $builder->create(PolicyInterface::class, [
            new WithReturn('isCompliant', ['name', $object, $context], true),
        ]);

        /** @var FieldDenormalizerInterface $nameFieldDenormalizer1 */
        $nameFieldDenormalizer1 = $builder->create(FieldDenormalizerInterface::class, [
            new WithCallback('denormalizeField', static function (
                string $givenPath,
                object $givenObject,
                mixed $givenValue,
                DenormalizerContextInterface $givenContext,
                ?DenormalizerInterface $givenDenormalizer = null
            ) use ($object, $context): void {
                self::assertSame('name', $givenPath);
                self::assertSame($object, $givenObject);
                self::assertSame('name', $givenValue);
                self::assertSame($context, $givenContext);
                self::assertInstanceOf(DenormalizerInterface::class, $givenDenormalizer);
            }),
        ]);

        /** @var DenormalizationFieldMappingInterface $denormalizationNameFieldMapping1 */
        $denormalizationNameFieldMapping1 = $builder->create(DenormalizationFieldMappingInterface::class, [
            new WithReturn('getName', [], 'name'),
            new WithReturn('getPolicy', [], $namePolicy1),
            new WithReturn('getFieldDenormalizer', [], $nameFieldDenormalizer1),
        ]);

        /** @var PolicyInterface $namePolicy2 */
        $namePolicy2 = $builder->create(PolicyInterface::class, [
            new WithReturn('isCompliant', ['name', $object, $context], true),
        ]);

        /** @var FieldDenormalizerInterface $nameFieldDenormalizer2 */
        $nameFieldDenormalizer2 = $builder->create(FieldDenormalizerInterface::class, [
            new WithCallback('denormalizeField', static function (
                string $givenPath,
                object $givenObject,
                mixed $givenValue,
                DenormalizerContextInterface $givenContext,
                ?DenormalizerInterface $givenDenormalizer = null
            ) use ($object, $context): void {
                self::assertSame('name', $givenPath);
                self::assertSame($object, $givenObject);
                self::assertSame('name', $givenValue);
                self::assertSame($context, $givenContext);
                self::assertInstanceOf(DenormalizerInterface::class, $givenDenormalizer);
            }),
        ]);

        /** @var DenormalizationFieldMappingInterface $denormalizationNameFieldMapping2 */
        $denormalizationNameFieldMapping2 = $builder->create(DenormalizationFieldMappingInterface::class, [
            new WithReturn('getName', [], 'name'),
            new WithReturn('getPolicy', [], $namePolicy2),
            new WithReturn('getFieldDenormalizer', [], $nameFieldDenormalizer2),
        ]);

        /** @var DenormalizationObjectMappingInterface $objectMapping */
        $objectMapping = $builder->create(DenormalizationObjectMappingInterface::class, [
            new WithReturn('getDenormalizationFieldMappings', ['', null], [
                $denormalizationNameFieldMapping1,
                $denormalizationNameFieldMapping2,
            ]),
        ]);

        /** @var DenormalizerObjectMappingRegistryInterface $registry */
        $registry = $builder->create(DenormalizerObjectMappingRegistryInterface::class, [
            new WithReturn('getObjectMapping', [\stdClass::class], $objectMapping),
        ]);

        /** @var LoggerInterface $logger */
        $logger = $builder->create(LoggerInterface::class, [
            new WithoutReturn('info', ['deserialize: path {path}', ['path' => 'name']]),
            new WithoutReturn('info', ['deserialize: path {path}', ['path' => 'name']]),
        ]);

        $denormalizer = new Denormalizer($registry, $logger);

        self::assertSame($object, $denormalizer->denormalize($object, ['name' => 'name'], $context));
    }

    public function testIsCleanMissing(): void
    {
        $object = new \stdClass();

        $factory = static fn () => $object;

        $builder = new MockObjectBuilder();

        /** @var DenormalizerContextInterface $context */
        $context = $builder->create(DenormalizerContextInterface::class, [
            new WithReturn('isClearMissing', [], true),
            new WithReturn('getAllowedAdditionalFields', [], null),
        ]);

        /** @var PolicyInterface $namePolicy */
        $namePolicy = $builder->create(PolicyInterface::class, [
            new WithReturn('isCompliant', ['name', $object, $context], true),
        ]);

        /** @var FieldDenormalizerInterface $nameFieldDenormalizer */
        $nameFieldDenormalizer = $builder->create(FieldDenormalizerInterface::class, [
            new WithCallback('denormalizeField', static function (
                string $givenPath,
                object $givenObject,
                mixed $givenValue,
                DenormalizerContextInterface $givenContext,
                ?DenormalizerInterface $givenDenormalizer = null
            ) use ($object, $context): void {
                self::assertSame('name', $givenPath);
                self::assertSame($object, $givenObject);
                self::assertSame('name', $givenValue);
                self::assertSame($context, $givenContext);
                self::assertInstanceOf(DenormalizerInterface::class, $givenDenormalizer);
            }),
        ]);

        /** @var DenormalizationFieldMappingInterface $denormalizationNameFieldMapping */
        $denormalizationNameFieldMapping = $builder->create(DenormalizationFieldMappingInterface::class, [
            new WithReturn('getName', [], 'name'),
            new WithReturn('getPolicy', [], $namePolicy),
            new WithReturn('getFieldDenormalizer', [], $nameFieldDenormalizer),
        ]);

        /** @var PolicyInterface $valuePolicy */
        $valuePolicy = $builder->create(PolicyInterface::class, [
            new WithReturn('isCompliant', ['value', $object, $context], true),
        ]);

        /** @var FieldDenormalizerInterface $valueFieldDenormalizer */
        $valueFieldDenormalizer = $builder->create(FieldDenormalizerInterface::class, [
            new WithCallback('denormalizeField', static function (
                string $givenPath,
                object $givenObject,
                mixed $givenValue,
                DenormalizerContextInterface $givenContext,
                ?DenormalizerInterface $givenDenormalizer = null
            ) use ($object, $context): void {
                self::assertSame('value', $givenPath);
                self::assertSame($object, $givenObject);
                self::assertNull($givenValue);
                self::assertSame($context, $givenContext);
                self::assertInstanceOf(DenormalizerInterface::class, $givenDenormalizer);
            }),
        ]);

        /** @var DenormalizationFieldMappingInterface $denormalizationValueFieldMapping */
        $denormalizationValueFieldMapping = $builder->create(DenormalizationFieldMappingInterface::class, [
            new WithReturn('getName', [], 'value'),
            new WithReturn('getPolicy', [], $valuePolicy),
            new WithReturn('getFieldDenormalizer', [], $valueFieldDenormalizer),
        ]);

        /** @var DenormalizationObjectMappingInterface $objectMapping */
        $objectMapping = $builder->create(DenormalizationObjectMappingInterface::class, [
            new WithReturn('getDenormalizationFactory', ['', 'object'], $factory),
            new WithReturn('getDenormalizationFieldMappings', ['', 'object'], [
                $denormalizationNameFieldMapping,
                $denormalizationValueFieldMapping,
            ]),
        ]);

        /** @var DenormalizerObjectMappingRegistryInterface $registry */
        $registry = $builder->create(DenormalizerObjectMappingRegistryInterface::class, [
            new WithReturn('getObjectMapping', [\stdClass::class], $objectMapping),
        ]);

        /** @var LoggerInterface $logger */
        $logger = $builder->create(LoggerInterface::class, [
            new WithoutReturn('info', ['deserialize: path {path}', ['path' => 'name']]),
            new WithoutReturn('info', ['deserialize: path {path}', ['path' => 'value']]),
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

        $factory = static fn () => 'string';

        $builder = new MockObjectBuilder();

        /** @var DenormalizerContextInterface $context */
        $context = $builder->create(DenormalizerContextInterface::class, []);

        /** @var DenormalizationObjectMappingInterface $objectMapping */
        $objectMapping = $builder->create(DenormalizationObjectMappingInterface::class, [
            new WithReturn('getDenormalizationFactory', ['', null], $factory),
        ]);

        /** @var DenormalizerObjectMappingRegistryInterface $registry */
        $registry = $builder->create(DenormalizerObjectMappingRegistryInterface::class, [
            new WithReturn('getObjectMapping', [\stdClass::class], $objectMapping),
        ]);

        /** @var LoggerInterface $logger */
        $logger = $builder->create(LoggerInterface::class, [
            new WithoutReturn('error', ['deserialize: {exception}', ['exception' => 'Factory does not return object, "string" given at path: ""']]),
        ]);

        $denormalizer = new Denormalizer($registry, $logger);
        $denormalizer->denormalize(\stdClass::class, ['name' => 'name'], $context);
    }

    public function testDenormalizeWithAdditionalData(): void
    {
        $this->expectException(DeserializerRuntimeException::class);
        $this->expectExceptionMessage('There are additional field(s) at paths: "additionalData"');

        $object = new \stdClass();

        $factory = static fn () => $object;

        $builder = new MockObjectBuilder();

        /** @var DenormalizerContextInterface $context */
        $context = $builder->create(DenormalizerContextInterface::class, [
            new WithReturn('isClearMissing', [], false),
            new WithReturn('isClearMissing', [], false),
            new WithReturn('getAllowedAdditionalFields', [], []),
        ]);

        /** @var DenormalizationFieldMappingInterface $denormalizationNameFieldMapping */
        $denormalizationNameFieldMapping = $builder->create(DenormalizationFieldMappingInterface::class, [
            new WithReturn('getName', [], 'name'),
        ]);

        /** @var DenormalizationFieldMappingInterface $denormalizationValueFieldMapping */
        $denormalizationValueFieldMapping = $builder->create(DenormalizationFieldMappingInterface::class, [
            new WithReturn('getName', [], 'value'),
        ]);

        /** @var DenormalizationObjectMappingInterface $objectMapping */
        $objectMapping = $builder->create(DenormalizationObjectMappingInterface::class, [
            new WithReturn('getDenormalizationFactory', ['', null], $factory),
            new WithReturn('getDenormalizationFieldMappings', ['', null], [
                $denormalizationNameFieldMapping,
                $denormalizationValueFieldMapping,
            ]),
        ]);

        /** @var DenormalizerObjectMappingRegistryInterface $registry */
        $registry = $builder->create(DenormalizerObjectMappingRegistryInterface::class, [
            new WithReturn('getObjectMapping', [\stdClass::class], $objectMapping),
        ]);

        /** @var LoggerInterface $logger */
        $logger = $builder->create(LoggerInterface::class, [
            new WithoutReturn('notice', ['deserialize: {exception}', ['exception' => 'There are additional field(s) at paths: "additionalData"']]),
        ]);

        $denormalizer = new Denormalizer($registry, $logger);
        $denormalizer->denormalize(\stdClass::class, ['additionalData' => 'additionalData'], $context);
    }

    public function testDenormalizeWithKeyCastToIntegerAdditionalDataExpectsException(): void
    {
        $this->expectException(DeserializerRuntimeException::class);
        $this->expectExceptionMessage('There are additional field(s) at paths: "1"');

        $object = new \stdClass();

        $factory = static fn () => $object;

        $builder = new MockObjectBuilder();

        /** @var DenormalizerContextInterface $context */
        $context = $builder->create(DenormalizerContextInterface::class, [
            new WithReturn('isClearMissing', [], false),
            new WithReturn('isClearMissing', [], false),
            new WithReturn('getAllowedAdditionalFields', [], []),
        ]);

        /** @var DenormalizationFieldMappingInterface $denormalizationNameFieldMapping */
        $denormalizationNameFieldMapping = $builder->create(DenormalizationFieldMappingInterface::class, [
            new WithReturn('getName', [], 'name'),
        ]);

        /** @var DenormalizationFieldMappingInterface $denormalizationValueFieldMapping */
        $denormalizationValueFieldMapping = $builder->create(DenormalizationFieldMappingInterface::class, [
            new WithReturn('getName', [], 'value'),
        ]);

        /** @var DenormalizationObjectMappingInterface $objectMapping */
        $objectMapping = $builder->create(DenormalizationObjectMappingInterface::class, [
            new WithReturn('getDenormalizationFactory', ['', null], $factory),
            new WithReturn('getDenormalizationFieldMappings', ['', null], [
                $denormalizationNameFieldMapping,
                $denormalizationValueFieldMapping,
            ]),
        ]);

        /** @var DenormalizerObjectMappingRegistryInterface $registry */
        $registry = $builder->create(DenormalizerObjectMappingRegistryInterface::class, [
            new WithReturn('getObjectMapping', [\stdClass::class], $objectMapping),
        ]);

        /** @var LoggerInterface $logger */
        $logger = $builder->create(LoggerInterface::class, [
            new WithoutReturn('notice', ['deserialize: {exception}', ['exception' => 'There are additional field(s) at paths: "1"']]),
        ]);

        $denormalizer = new Denormalizer($registry, $logger);
        $denormalizer->denormalize(\stdClass::class, ['1' => 'additionalData'], $context);
    }

    #[DoesNotPerformAssertions]
    public function testDenormalizeWithAdditionalDataAndAllowIt(): void
    {
        $object = new \stdClass();

        $factory = static fn () => $object;

        $builder = new MockObjectBuilder();

        /** @var DenormalizerContextInterface $context */
        $context = $builder->create(DenormalizerContextInterface::class, [
            new WithReturn('isClearMissing', [], false),
            new WithReturn('isClearMissing', [], false),
            new WithReturn('getAllowedAdditionalFields', [], null),
        ]);

        /** @var DenormalizationFieldMappingInterface $denormalizationNameFieldMapping */
        $denormalizationNameFieldMapping = $builder->create(DenormalizationFieldMappingInterface::class, [
            new WithReturn('getName', [], 'name'),
        ]);

        /** @var DenormalizationFieldMappingInterface $denormalizationValueFieldMapping */
        $denormalizationValueFieldMapping = $builder->create(DenormalizationFieldMappingInterface::class, [
            new WithReturn('getName', [], 'value'),
        ]);

        /** @var DenormalizationObjectMappingInterface $objectMapping */
        $objectMapping = $builder->create(DenormalizationObjectMappingInterface::class, [
            new WithReturn('getDenormalizationFactory', ['', null], $factory),
            new WithReturn('getDenormalizationFieldMappings', ['', null], [
                $denormalizationNameFieldMapping,
                $denormalizationValueFieldMapping,
            ]),
        ]);

        /** @var DenormalizerObjectMappingRegistryInterface $registry */
        $registry = $builder->create(DenormalizerObjectMappingRegistryInterface::class, [
            new WithReturn('getObjectMapping', [\stdClass::class], $objectMapping),
        ]);

        /** @var LoggerInterface $logger */
        $logger = $builder->create(LoggerInterface::class, []);

        $denormalizer = new Denormalizer($registry, $logger);
        $denormalizer->denormalize(\stdClass::class, ['additionalData' => 'additionalData'], $context);
    }

    public function testDenormalizeWithMissingObjectMapping(): void
    {
        $this->expectException(DeserializerLogicException::class);
        $this->expectExceptionMessage('There is no mapping for class: "stdClass"');

        $exception = DeserializerLogicException::createMissingMapping(\stdClass::class);

        $builder = new MockObjectBuilder();

        /** @var DenormalizerContextInterface $context */
        $context = $builder->create(DenormalizerContextInterface::class, []);

        /** @var DenormalizerObjectMappingRegistryInterface $registry */
        $registry = $builder->create(DenormalizerObjectMappingRegistryInterface::class, [
            new WithException('getObjectMapping', [\stdClass::class], $exception),
        ]);

        /** @var LoggerInterface $logger */
        $logger = $builder->create(LoggerInterface::class, [
            new WithReturn('error', ['deserialize: {exception}', ['exception' => 'There is no mapping for class: "stdClass"']], null),
        ]);

        $denormalizer = new Denormalizer($registry, $logger);
        $denormalizer->denormalize(\stdClass::class, ['name' => 'name'], $context);
    }

    public function testDenormalizeWithNotCompliantPolicy(): void
    {
        $object = new \stdClass();

        $factory = static fn () => $object;

        $builder = new MockObjectBuilder();

        /** @var DenormalizerContextInterface $context */
        $context = $builder->create(DenormalizerContextInterface::class, [
            new WithReturn('isClearMissing', [], false),
            new WithReturn('getAllowedAdditionalFields', [], null),
        ]);

        /** @var PolicyInterface $namePolicy */
        $namePolicy = $builder->create(PolicyInterface::class, [
            new WithReturn('isCompliant', ['name', $object, $context], false),
        ]);

        /** @var DenormalizationFieldMappingInterface $denormalizationNameFieldMapping */
        $denormalizationNameFieldMapping = $builder->create(DenormalizationFieldMappingInterface::class, [
            new WithReturn('getName', [], 'name'),
            new WithReturn('getPolicy', [], $namePolicy),
        ]);

        /** @var DenormalizationFieldMappingInterface $denormalizationValueFieldMapping */
        $denormalizationValueFieldMapping = $builder->create(DenormalizationFieldMappingInterface::class, [
            new WithReturn('getName', [], 'value'),
        ]);

        /** @var DenormalizationObjectMappingInterface $objectMapping */
        $objectMapping = $builder->create(DenormalizationObjectMappingInterface::class, [
            new WithReturn('getDenormalizationFactory', ['', null], $factory),
            new WithReturn('getDenormalizationFieldMappings', ['', null], [
                $denormalizationNameFieldMapping,
                $denormalizationValueFieldMapping,
            ]),
        ]);

        /** @var DenormalizerObjectMappingRegistryInterface $registry */
        $registry = $builder->create(DenormalizerObjectMappingRegistryInterface::class, [
            new WithReturn('getObjectMapping', [\stdClass::class], $objectMapping),
        ]);

        /** @var LoggerInterface $logger */
        $logger = $builder->create(LoggerInterface::class, []);

        $denormalizer = new Denormalizer($registry, $logger);

        self::assertSame($object, $denormalizer->denormalize(\stdClass::class, ['name' => 'name'], $context));
    }

    public function testDenormalizeWithInvalidType(): void
    {
        $this->expectException(DeserializerRuntimeException::class);
        $this->expectExceptionMessage('Type is not a string, "array" given at path: ""');

        $builder = new MockObjectBuilder();

        /** @var DenormalizerContextInterface $context */
        $context = $builder->create(DenormalizerContextInterface::class, []);

        /** @var DenormalizerObjectMappingRegistryInterface $registry */
        $registry = $builder->create(DenormalizerObjectMappingRegistryInterface::class, []);

        /** @var LoggerInterface $logger */
        $logger = $builder->create(LoggerInterface::class, [
            new WithReturn('error', ['deserialize: {exception}', ['exception' => 'Type is not a string, "array" given at path: ""']], null),
        ]);

        $denormalizer = new Denormalizer($registry, $logger);
        $denormalizer->denormalize(\stdClass::class, ['_type' => ['key' => 'value']], $context);
    }
}
