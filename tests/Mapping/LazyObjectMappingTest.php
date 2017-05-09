<?php

declare(strict_types=1);

namespace Chubbyphp\Tests\Deserialization\Mapping;

use Chubbyphp\Deserialization\Mapping\PropertyMappingInterface;
use Chubbyphp\Deserialization\Mapping\LazyObjectMapping;
use Chubbyphp\Deserialization\Mapping\ObjectMappingInterface;
use Interop\Container\ContainerInterface;

/**
 * @covers \Chubbyphp\Deserialization\Mapping\LazyObjectMapping
 */
final class LazyObjectMappingTest extends \PHPUnit_Framework_TestCase
{
    public function testInvoke()
    {
        $propertyMappings = [$this->getPropertyMapping()];

        $factory = function () {
        };

        $container = $this->getContainer([
            'service' => $this->getObjectMapping('class', $factory, $propertyMappings),
        ]);

        $objectMapping = new LazyObjectMapping($container, 'service', 'class');

        self::assertSame('class', $objectMapping->getClass());
        self::assertSame($factory, $objectMapping->getFactory());
        self::assertSame($propertyMappings, $objectMapping->getPropertyMappings());
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
     * @param string   $class
     * @param callable $factory
     * @param array    $propertyMappings
     *
     * @return ObjectMappingInterface
     */
    private function getObjectMapping(
        string $class,
        callable $factory,
        array $propertyMappings
    ): ObjectMappingInterface {
        /** @var ObjectMappingInterface|\PHPUnit_Framework_MockObject_MockObject $mapping */
        $mapping = $this
            ->getMockBuilder(ObjectMappingInterface::class)
            ->setMethods(['getClass', 'getFactory', 'getPropertyMappings'])
            ->getMockForAbstractClass()
        ;

        $mapping->expects(self::any())->method('getClass')->willReturn($class);
        $mapping->expects(self::any())->method('getFactory')->willReturn($factory);
        $mapping->expects(self::any())->method('getPropertyMappings')->willReturn($propertyMappings);

        return $mapping;
    }

    /**
     * @return PropertyMappingInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private function getPropertyMapping(): PropertyMappingInterface
    {
        return $this->getMockBuilder(PropertyMappingInterface::class)->getMockForAbstractClass();
    }
}
