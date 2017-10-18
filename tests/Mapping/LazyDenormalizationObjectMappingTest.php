<?php

declare(strict_types=1);

namespace Chubbyphp\Tests\Deserialization\Mapping;

use Chubbyphp\Deserialization\Mapping\DenormalizationFieldMappingInterface;
use Chubbyphp\Deserialization\Mapping\DenormalizationObjectMappingInterface;
use Chubbyphp\Deserialization\Mapping\LazyDenormalizationObjectMapping;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;

/**
 * @covers \Chubbyphp\Deserialization\Mapping\LazyDenormalizationObjectMapping
 */
class LazyDenormalizationObjectMappingTest extends TestCase
{
    public function testInvoke()
    {
        $denormalizationFieldMappings = [$this->getDenormalizationFieldMapping()];

        $factory = function () {
        };

        $container = $this->getContainer([
            'service' => $this->getDenormalizationObjectMapping($factory, $denormalizationFieldMappings),
        ]);

        $objectMapping = new LazyDenormalizationObjectMapping($container, 'service', \stdClass::class);

        self::assertEquals(\stdClass::class, $objectMapping->getClass());
        self::assertSame($factory, $objectMapping->getDenormalizationFactory('path', 'type'));
        self::assertSame($denormalizationFieldMappings, $objectMapping->getDenormalizationFieldMappings('path', 'type'));
    }

    /**
     * @param array $services
     *
     * @return ContainerInterface
     */
    private function getContainer(array $services): ContainerInterface
    {
        /** @var ContainerInterface|\PHPUnit_Framework_MockObject_MockObject $container */
        $container = $this->getMockBuilder(ContainerInterface::class)->setMethods(['get'])->getMockForAbstractClass();

        $container
            ->expects(self::any())
            ->method('get')
            ->willReturnCallback(function (string $id) use ($services) {
                return $services[$id];
            })
        ;

        return $container;
    }

    /**
     * @param callable $denormalizationFactory
     * @param array    $denormalizationFieldMappings
     *
     * @return DenormalizationObjectMappingInterface
     */
    private function getDenormalizationObjectMapping(
        callable $denormalizationFactory,
        array $denormalizationFieldMappings
    ): DenormalizationObjectMappingInterface {
        /** @var DenormalizationObjectMappingInterface|\PHPUnit_Framework_MockObject_MockObject $mapping */
        $mapping = $this
            ->getMockBuilder(DenormalizationObjectMappingInterface::class)
            ->setMethods(['getDenormalizationFactory', 'getDenormalizationFieldMappings'])
            ->getMockForAbstractClass()
        ;

        $mapping->expects(self::any())
            ->method('getDenormalizationFactory')
            ->with(self::equalTo('path'), self::equalTo('type'))
            ->willReturn($denormalizationFactory);

        $mapping->expects(self::any())
            ->method('getDenormalizationFieldMappings')
            ->with(self::equalTo('path'), self::equalTo('type'))
            ->willReturn($denormalizationFieldMappings);

        return $mapping;
    }

    /**
     * @return DenormalizationFieldMappingInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private function getDenormalizationFieldMapping(): DenormalizationFieldMappingInterface
    {
        return $this->getMockBuilder(DenormalizationFieldMappingInterface::class)->getMockForAbstractClass();
    }
}
