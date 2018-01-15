<?php

declare(strict_types=1);

namespace Chubbyphp\Tests\Deserialization\Denormalizer;

use Chubbyphp\Deserialization\Denormalizer\DenormalizerObjectMappingRegistry;
use Chubbyphp\Deserialization\DeserializerLogicException;
use Chubbyphp\Deserialization\Mapping\DenormalizationObjectMappingInterface;
use Chubbyphp\Tests\Deserialization\Resources\Model\AbstractManyModel;
use Doctrine\Common\Persistence\Proxy;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Chubbyphp\Deserialization\Denormalizer\DenormalizerObjectMappingRegistry
 */
class DenormalizerObjectMappingRegistryTest extends TestCase
{
    public function testGetObjectMapping()
    {
        $object = $this->getObject();

        $registry = new DenormalizerObjectMappingRegistry([
            $this->getDenormalizationObjectMapping(),
        ]);

        $mapping = $registry->getObjectMapping(get_class($object));

        self::assertInstanceOf(DenormalizationObjectMappingInterface::class, $mapping);
    }

    public function testGetMissingObjectMapping()
    {
        self::expectException(DeserializerLogicException::class);
        self::expectExceptionMessage('There is no mapping for class: "stdClass"');

        $registry = new DenormalizerObjectMappingRegistry([]);

        $registry->getObjectMapping(get_class(new \stdClass()));
    }

    public function testGetObjectMappingFromDoctrineProxy()
    {
        $object = $this->getProxyObject();

        $registry = new DenormalizerObjectMappingRegistry([
            $this->getDenormalizationProxyObjectMapping(),
        ]);

        $mapping = $registry->getObjectMapping(get_class($object));

        self::assertInstanceOf(DenormalizationObjectMappingInterface::class, $mapping);
    }

    /**
     * @return DenormalizationObjectMappingInterface
     */
    private function getDenormalizationObjectMapping(): DenormalizationObjectMappingInterface
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

        return $objectMapping;
    }

    /**
     * @return DenormalizationObjectMappingInterface
     */
    private function getDenormalizationProxyObjectMapping(): DenormalizationObjectMappingInterface
    {
        /** @var DenormalizationObjectMappingInterface|\PHPUnit_Framework_MockObject_MockObject $objectMapping */
        $objectMapping = $this->getMockBuilder(DenormalizationObjectMappingInterface::class)
            ->setMethods([])
            ->getMockForAbstractClass();

        $object = $this->getProxyObject();

        $objectMapping->expects(self::any())->method('getClass')->willReturnCallback(
            function () use ($object) {
                return AbstractManyModel::class;
            }
        );

        return $objectMapping;
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
             *
             * @return void
             */
            public function __load()
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
