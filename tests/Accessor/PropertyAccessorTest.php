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
             * Initializes this proxy if its not yet initialized.
             *
             * Acts as a no-op if already initialized.
             */
            public function __load()
            {
                // TODO: Implement __load() method.
            }

            /**
             * Returns whether this proxy is initialized or not.
             *
             * @return bool
             */
            public function __isInitialized()
            {
                // TODO: Implement __isInitialized() method.
            }
        };

        $accessor = new PropertyAccessor('address');

        $accessor->setValue($object, 'Address');

        self::assertSame('Address', $accessor->getValue($object));

        $error = error_get_last();

        self::assertSame(E_USER_DEPRECATED, $error['type']);
        self::assertSame('"Chubbyphp\Tests\Deserialization\Resources\Model\AbstractManyModel" got a proxy "Doctrine\Common\Persistence\Proxy", use "Chubbyphp\Deserialization\Doctrine\Accessor\PropertyAccessor" instead of "Chubbyphp\Deserialization\Accessor\PropertyAccessor', $error['message']);
    }

    public function testMissingSet()
    {
        self::expectException(DeserializerLogicException::class);

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
             * Initializes this proxy if its not yet initialized.
             *
             * Acts as a no-op if already initialized.
             */
            public function __load()
            {
                // TODO: Implement __load() method.
            }

            /**
             * Returns whether this proxy is initialized or not.
             *
             * @return bool
             */
            public function __isInitialized()
            {
                // TODO: Implement __isInitialized() method.
            }
        };

        $object->setAddress('Address');

        $accessor = new PropertyAccessor('address');

        self::assertSame('Address', $accessor->getValue($object));

        $error = error_get_last();

        self::assertSame(E_USER_DEPRECATED, $error['type']);
        self::assertSame('"Chubbyphp\Tests\Deserialization\Resources\Model\AbstractManyModel" got a proxy "Doctrine\Common\Persistence\Proxy", use "Chubbyphp\Deserialization\Doctrine\Accessor\PropertyAccessor" instead of "Chubbyphp\Deserialization\Accessor\PropertyAccessor', $error['message']);
    }

    public function testMissingGet()
    {
        self::expectException(DeserializerLogicException::class);

        $object = new class() {
        };

        $accessor = new PropertyAccessor('name');
        $accessor->getValue($object);
    }
}
