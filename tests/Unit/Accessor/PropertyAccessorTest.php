<?php

declare(strict_types=1);

namespace Chubbyphp\Tests\Deserialization\Unit\Accessor;

use Chubbyphp\Deserialization\Accessor\PropertyAccessor;
use Chubbyphp\Deserialization\DeserializerLogicException;
use Chubbyphp\Tests\Deserialization\Resources\Model\AbstractManyModel;
use Doctrine\Persistence\Proxy;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Chubbyphp\Deserialization\Accessor\PropertyAccessor
 *
 * @internal
 */
final class PropertyAccessorTest extends TestCase
{
    public function testSetValue(): void
    {
        $object = new class {
            private string $name;

            public function getName(): string
            {
                return $this->name;
            }
        };

        $accessor = new PropertyAccessor('name');
        $accessor->setValue($object, 'Name');

        self::assertSame('Name', $object->getName());
    }

    public function testSetValueCanAccessPrivatePropertyThroughDoctrineProxyClass(): void
    {
        $object = new class extends AbstractManyModel implements Proxy {
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

        $accessor = new PropertyAccessor('address');

        self::assertFalse($object->__isInitialized());

        $accessor->setValue($object, 'Address');

        self::assertTrue($object->__isInitialized());

        self::assertSame('Address', $object->getAddress());
    }

    public function testMissingSet(): void
    {
        $this->expectException(DeserializerLogicException::class);

        $object = new class {};

        $accessor = new PropertyAccessor('name');
        $accessor->setValue($object, 'Name');
    }

    public function testGetValue(): void
    {
        $object = new class {
            private string $name;

            public function setName(string $name): void
            {
                $this->name = $name;
            }

            public function getName(): string
            {
                return $this->name;
            }
        };

        $object->setName('Name');

        $accessor = new PropertyAccessor('name');

        self::assertSame('Name', $accessor->getValue($object));
    }

    public function testGetValueHandleUninitializedProperty(): void
    {
        $object = new class {
            private string $name;

            public function getName(): string
            {
                return $this->name;
            }
        };

        $accessor = new PropertyAccessor('name');

        self::assertNull($accessor->getValue($object));
    }

    public function testGetValueCanAccessPrivatePropertyThroughDoctrineProxyClass(): void
    {
        $object = new class extends AbstractManyModel implements Proxy {
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

        $object->setAddress('Address');

        $accessor = new PropertyAccessor('address');

        self::assertFalse($object->__isInitialized());

        self::assertSame('Address', $accessor->getValue($object));

        self::assertTrue($object->__isInitialized());
    }

    public function testMissingGet(): void
    {
        $this->expectException(DeserializerLogicException::class);

        $object = new class {};

        $accessor = new PropertyAccessor('name');
        $accessor->getValue($object);
    }
}
