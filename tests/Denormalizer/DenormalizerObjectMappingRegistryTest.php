<?php

declare(strict_types=1);

namespace Chubbyphp\Tests\Deserialization\Denormalizer;

use Chubbyphp\Deserialization\Denormalizer\DenormalizerObjectMappingRegistry;
use Chubbyphp\Deserialization\DeserializerLogicException;
use Chubbyphp\Deserialization\Mapping\DenormalizationObjectMappingInterface;
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
