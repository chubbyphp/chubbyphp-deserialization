<?php

declare(strict_types=1);

namespace Chubbyphp\Tests\Deserialization\Unit\Denormalizer;

use Chubbyphp\Deserialization\Denormalizer\DenormalizerObjectMappingRegistry;
use Chubbyphp\Deserialization\DeserializerLogicException;
use Chubbyphp\Deserialization\Mapping\DenormalizationObjectMappingInterface;
use Chubbyphp\Mock\Call;
use Chubbyphp\Mock\MockByCallsTrait;
use Chubbyphp\Tests\Deserialization\Resources\Model\AbstractManyModel;
use Doctrine\Common\Persistence\Proxy;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Chubbyphp\Deserialization\Denormalizer\DenormalizerObjectMappingRegistry
 *
 * @internal
 */
final class DenormalizerObjectMappingRegistryTest extends TestCase
{
    use MockByCallsTrait;

    public function testGetObjectMapping(): void
    {
        $object = new \stdClass();

        /** @var DenormalizationObjectMappingInterface|MockObject $denormalizationObjectMapping */
        $denormalizationObjectMapping = $this->getMockByCalls(DenormalizationObjectMappingInterface::class, [
            Call::create('getClass')->with()->willReturn(\stdClass::class),
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

        $registry->getObjectMapping(get_class(new \stdClass()));
    }

    public function testGetObjectMappingFromDoctrineProxy(): void
    {
        $object = $this->getProxyObject();

        /** @var DenormalizationObjectMappingInterface|MockObject $denormalizationObjectMapping */
        $denormalizationObjectMapping = $this->getMockByCalls(DenormalizationObjectMappingInterface::class, [
            Call::create('getClass')->with()->willReturn(AbstractManyModel::class),
        ]);

        $registry = new DenormalizerObjectMappingRegistry([$denormalizationObjectMapping]);

        $mapping = $registry->getObjectMapping(get_class($object));

        self::assertInstanceOf(DenormalizationObjectMappingInterface::class, $mapping);
    }

    /**
     * @return object
     */
    private function getProxyObject()
    {
        return new class() extends AbstractManyModel implements Proxy {
            /**
             * Initializes this proxy if its not yet initialized.
             *
             * Acts as a no-op if already initialized.
             */
            public function __load(): void
            {
            }

            /**
             * Returns whether this proxy is initialized or not.
             *
             * @return bool
             */
            public function __isInitialized()
            {
            }
        };
    }
}
