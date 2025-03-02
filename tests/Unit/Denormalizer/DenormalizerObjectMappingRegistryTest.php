<?php

declare(strict_types=1);

namespace Chubbyphp\Tests\Deserialization\Unit\Denormalizer;

use Chubbyphp\Deserialization\Denormalizer\DenormalizerObjectMappingRegistry;
use Chubbyphp\Deserialization\DeserializerLogicException;
use Chubbyphp\Deserialization\Mapping\DenormalizationObjectMappingInterface;
use Chubbyphp\Mock\MockMethod\WithReturn;
use Chubbyphp\Mock\MockObjectBuilder;
use Chubbyphp\Tests\Deserialization\Resources\Model\AbstractManyModel;
use Doctrine\Persistence\Proxy;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Chubbyphp\Deserialization\Denormalizer\DenormalizerObjectMappingRegistry
 *
 * @internal
 */
final class DenormalizerObjectMappingRegistryTest extends TestCase
{
    public function testGetObjectMapping(): void
    {
        $builder = new MockObjectBuilder();

        /** @var DenormalizationObjectMappingInterface $denormalizationObjectMapping */
        $denormalizationObjectMapping = $builder->create(DenormalizationObjectMappingInterface::class, [
            new WithReturn('getClass', [], \stdClass::class),
        ]);

        $registry = new DenormalizerObjectMappingRegistry([$denormalizationObjectMapping]);

        $mapping = $registry->getObjectMapping(\stdClass::class);

        self::assertInstanceOf(DenormalizationObjectMappingInterface::class, $mapping);
    }

    public function testGetMissingObjectMapping(): void
    {
        $this->expectException(DeserializerLogicException::class);
        $this->expectExceptionMessage('There is no mapping for class: "stdClass"');

        $registry = new DenormalizerObjectMappingRegistry([]);

        $registry->getObjectMapping((new \stdClass())::class);
    }

    public function testGetObjectMappingFromDoctrineProxy(): void
    {
        $object = $this->getProxyObject();

        $builder = new MockObjectBuilder();

        /** @var DenormalizationObjectMappingInterface $denormalizationObjectMapping */
        $denormalizationObjectMapping = $builder->create(DenormalizationObjectMappingInterface::class, [
            new WithReturn('getClass', [], AbstractManyModel::class),
        ]);

        $registry = new DenormalizerObjectMappingRegistry([$denormalizationObjectMapping]);

        $mapping = $registry->getObjectMapping($object::class);

        self::assertInstanceOf(DenormalizationObjectMappingInterface::class, $mapping);
    }

    private function getProxyObject(): object
    {
        return new class extends AbstractManyModel implements Proxy {
            private bool $initialized = false;

            public function __load(): void
            {
                $this->initialized = true;
            }

            public function __isInitialized(): bool
            {
                return $this->initialized;
            }
        };
    }
}
