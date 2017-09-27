<?php

declare(strict_types=1);

namespace Chubbyphp\Tests\Deserialization\Accessor;

use Chubbyphp\Deserialization\Accessor\AccessorException;
use Chubbyphp\Deserialization\Accessor\PropertyAccessor;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Chubbyphp\Deserialization\Accessor\PropertyAccessor
 */
class PropertyAccessorTest extends TestCase
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
        };

        $accessor = new PropertyAccessor('name');
        $accessor->setValue($model, 'Name');

        self::assertSame('Name', $model->getName());
    }

    public function testMissingSet()
    {
        self::expectException(AccessorException::class);

        $model = new class() {
        };

        $accessor = new PropertyAccessor('name');
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
             * @param string $name
             */
            public function setName(string $name)
            {
                $this->name = $name;
            }
        };

        $model->setName('Name');

        $accessor = new PropertyAccessor('name');

        self::assertSame('Name', $accessor->getValue($model));
    }

    public function testMissingGet()
    {
        self::expectException(AccessorException::class);

        $model = new class() {
        };

        $accessor = new PropertyAccessor('name');
        $accessor->getValue($model);
    }
}
