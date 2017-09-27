<?php

declare(strict_types=1);

namespace Chubbyphp\Tests\Deserialization\Accessor;

use Chubbyphp\Deserialization\Accessor\AccessorException;
use Chubbyphp\Deserialization\Accessor\MethodAccessor;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Chubbyphp\Deserialization\Accessor\MethodAccessor
 */
class MethodAccessorTest extends TestCase
{
    public function testSetValue()
    {
        $model = new class() {
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
        $accessor->setValue($model, 'Name');

        self::assertSame('Name', $model->getName());
    }

    public function testMissingSet()
    {
        self::expectException(AccessorException::class);

        $model = new class() {
        };

        $accessor = new MethodAccessor('name');
        $accessor->setValue($model, 'Name');
    }

    public function testGetValue()
    {
        $model = new class() {
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

        $model->setName('Name');

        $accessor = new MethodAccessor('name');

        self::assertSame('Name', $accessor->getValue($model));
    }

    public function testHasValue()
    {
        $model = new class() {
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

        $model->setName('Name');

        $accessor = new MethodAccessor('name');

        self::assertTrue($accessor->getValue($model));
    }

    public function testIsValue()
    {
        $model = new class() {
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

        $model->setName('Name');

        $accessor = new MethodAccessor('name');

        self::assertTrue($accessor->getValue($model));
    }

    public function testMissingGet()
    {
        self::expectException(AccessorException::class);

        $model = new class() {
        };

        $accessor = new MethodAccessor('name');
        $accessor->getValue($model);
    }
}
