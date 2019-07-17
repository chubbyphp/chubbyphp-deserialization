<?php

declare(strict_types=1);

namespace Chubbyphp\Tests\Deserialization\Accessor;

use Chubbyphp\Deserialization\Accessor\MethodAccessor;
use Chubbyphp\Deserialization\DeserializerLogicException;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Chubbyphp\Deserialization\Accessor\MethodAccessor
 *
 * @internal
 */
class MethodAccessorTest extends TestCase
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

            /**
             * @param string $name
             */
            public function setName(string $name)
            {
                $this->name = $name;
            }
        };

        $accessor = new MethodAccessor('name');
        $accessor->setValue($object, 'Name');

        self::assertSame('Name', $object->getName());
    }

    public function testMissingSet()
    {
        $this->expectException(DeserializerLogicException::class);

        $object = new class() {
        };

        $accessor = new MethodAccessor('name');
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
             * @return string
             */
            public function getName(): string
            {
                return $this->name;
            }

            /**
             * @param string $name
             */
            public function setName(string $name)
            {
                $this->name = $name;
            }
        };

        $object->setName('Name');

        $accessor = new MethodAccessor('name');

        self::assertSame('Name', $accessor->getValue($object));
    }

    public function testHasValue()
    {
        $object = new class() {
            /**
             * @var string
             */
            private $name;

            /**
             * @return bool
             */
            public function hasName(): bool
            {
                return (bool) $this->name;
            }

            /**
             * @param string $name
             */
            public function setName(string $name)
            {
                $this->name = $name;
            }
        };

        $object->setName('Name');

        $accessor = new MethodAccessor('name');

        self::assertTrue($accessor->getValue($object));
    }

    public function testIsValue()
    {
        $object = new class() {
            /**
             * @var string
             */
            private $name;

            /**
             * @return bool
             */
            public function isName(): bool
            {
                return (bool) $this->name;
            }

            /**
             * @param string $name
             */
            public function setName(string $name)
            {
                $this->name = $name;
            }
        };

        $object->setName('Name');

        $accessor = new MethodAccessor('name');

        self::assertTrue($accessor->getValue($object));
    }

    public function testMissingGet()
    {
        $this->expectException(DeserializerLogicException::class);

        $object = new class() {
        };

        $accessor = new MethodAccessor('name');
        $accessor->getValue($object);
    }
}
