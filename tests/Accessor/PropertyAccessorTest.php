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

        self::assertNull(error_get_last());
    }

    public function testSetValueCanAccessPrivatePropertyThroughDoctrineProxyClass()
    {
        $object = new class() extends AbstractManyModel implements Proxy {
            public function __load()
            {
            }

            /**
             * @return bool
             */
            public function __isInitialized()
            {
                return false;
            }
        };

        $accessor = new PropertyAccessor('address');

        $accessor->setValue($object, 'Address');

        self::assertSame('Address', $accessor->getValue($object));

        $error = error_get_last();

        error_clear_last();

        self::assertNotNull($error);

        self::assertSame(E_USER_DEPRECATED, $error['type']);
        self::assertSame('Use "Chubbyphp\Deserialization\Doctrine\Accessor\PropertyAccessor" instead of "Chubbyphp\Deserialization\Accessor\PropertyAccessor" for "Chubbyphp\Tests\Deserialization\Resources\Model\AbstractManyModel:address"', $error['message']);
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

        self::assertNull(error_get_last());
    }

    public function testGetValueCanAccessPrivatePropertyThroughDoctrineProxyClass()
    {
        $object = new class() extends AbstractManyModel implements Proxy {
            public function __load()
            {
            }

            /**
             * @return bool
             */
            public function __isInitialized()
            {
                return false;
            }
        };

        $object->setAddress('Address');

        $accessor = new PropertyAccessor('address');

        self::assertSame('Address', $accessor->getValue($object));

        $error = error_get_last();

        error_clear_last();

        self::assertNotNull($error);

        self::assertSame(E_USER_DEPRECATED, $error['type']);
        self::assertSame('Use "Chubbyphp\Deserialization\Doctrine\Accessor\PropertyAccessor" instead of "Chubbyphp\Deserialization\Accessor\PropertyAccessor" for "Chubbyphp\Tests\Deserialization\Resources\Model\AbstractManyModel:address"', $error['message']);
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
