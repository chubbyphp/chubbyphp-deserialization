<?php

declare(strict_types=1);

namespace Chubbyphp\Tests\Deserialization\Accessor;

use Chubbyphp\Deserialization\Accessor\PropertyAccessor;
use Chubbyphp\Deserialization\DeserializerLogicException;
use Chubbyphp\Tests\Deserialization\Resources\Model\AbstractManyModel;
use Doctrine\Common\Persistence\Proxy;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Chubbyphp\Deserialization\Accessor\PropertyAccessor
 *
 * @internal
 */
class PropertyAccessorTest extends TestCase
{
    public function testSetValue()
    {
        $object = new class() {
            /**
             * @var string
             */
            private $name;

            /**
             * @return string
             */
            public function getName(): string
            {
                return $this->name;
            }
        };

        $accessor = new PropertyAccessor('name');
        $accessor->setValue($object, 'Name');

        self::assertSame('Name', $object->getName());
    }

    public function testSetValueCanAccessPrivatePropertyThroughDoctrineProxyClass()
    {
        $object = new class() extends AbstractManyModel implements Proxy {
            /**
             * @var bool
             */
            private $initialized = false;

            public function __load()
            {
                $this->initialized = true;
            }

            /**
             * @return bool
             */
            public function __isInitialized()
            {
                return $this->initialized;
            }
        };

        $accessor = new PropertyAccessor('address');

        self::assertFalse($object->__isInitialized());

        $accessor->setValue($object, 'Address');

        self::assertTrue($object->__isInitialized());

        self::assertSame('Address', $object->getAddress());
    }

    public function testMissingSet()
    {
        $this->expectException(DeserializerLogicException::class);

        $object = new class() {
        };

        $accessor = new PropertyAccessor('name');
        $accessor->setValue($object, 'Name');
    }

    public function testGetValue()
    {
        $object = new class() {
            /**
             * @var string
             */
            private $name;

            /**
             * @param string $name
             */
            public function setName(string $name)
            {
                $this->name = $name;
            }
        };

        $object->setName('Name');

        $accessor = new PropertyAccessor('name');

        self::assertSame('Name', $accessor->getValue($object));
    }

    public function testGetValueCanAccessPrivatePropertyThroughDoctrineProxyClass()
    {
        $object = new class() extends AbstractManyModel implements Proxy {
            /**
             * @var bool
             */
            private $initialized = false;

            public function __load()
            {
                $this->initialized = true;
            }

            /**
             * @return bool
             */
            public function __isInitialized()
            {
                return $this->initialized;
            }
        };

        $object->setAddress('Address');

        $accessor = new PropertyAccessor('address');

        self::assertFalse($object->__isInitialized());

        self::assertSame('Address', $accessor->getValue($object));

        self::assertTrue($object->__isInitialized());
    }

    public function testMissingGet()
    {
        $this->expectException(DeserializerLogicException::class);

        $object = new class() {
        };

        $accessor = new PropertyAccessor('name');
        $accessor->getValue($object);
    }
}
