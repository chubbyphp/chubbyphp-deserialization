<?php

declare(strict_types=1);

namespace Chubbyphp\Tests\Deserialization\Unit\Accessor;

use Chubbyphp\Deserialization\Accessor\MethodAccessor;
use Chubbyphp\Deserialization\DeserializerLogicException;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Chubbyphp\Deserialization\Accessor\MethodAccessor
 *
 * @internal
 */
final class MethodAccessorTest extends TestCase
{
    public function testSetValue(): void
    {
        $object = new class {
            private ?string $name = null;

            public function getName(): string
            {
                return $this->name;
            }

            public function setName(string $name): void
            {
                $this->name = $name;
            }
        };

        $accessor = new MethodAccessor('name');
        $accessor->setValue($object, 'Name');

        self::assertSame('Name', $object->getName());
    }

    public function testMissingSet(): void
    {
        $this->expectException(DeserializerLogicException::class);

        $object = new class {};

        $accessor = new MethodAccessor('name');
        $accessor->setValue($object, 'Name');
    }

    public function testGetValue(): void
    {
        $object = new class {
            private ?string $name = null;

            public function getName(): string
            {
                return $this->name;
            }

            public function setName(string $name): void
            {
                $this->name = $name;
            }
        };

        $object->setName('Name');

        $accessor = new MethodAccessor('name');

        self::assertSame('Name', $accessor->getValue($object));
    }

    public function testHasValue(): void
    {
        $object = new class {
            private ?string $name = null;

            public function hasName(): bool
            {
                return (bool) $this->name;
            }

            public function setName(string $name): void
            {
                $this->name = $name;
            }
        };

        $object->setName('Name');

        $accessor = new MethodAccessor('name');

        self::assertTrue($accessor->getValue($object));
    }

    public function testIsValue(): void
    {
        $object = new class {
            private ?string $name = null;

            public function isName(): bool
            {
                return (bool) $this->name;
            }

            public function setName(string $name): void
            {
                $this->name = $name;
            }
        };

        $object->setName('Name');

        $accessor = new MethodAccessor('name');

        self::assertTrue($accessor->getValue($object));
    }

    public function testMissingGet(): void
    {
        $this->expectException(DeserializerLogicException::class);

        $object = new class {};

        $accessor = new MethodAccessor('name');
        $accessor->getValue($object);
    }
}
