<?php

declare(strict_types=1);

namespace Chubbyphp\Tests\Deserialization\Denormalizer\Relation\Doctrine;

use Chubbyphp\Deserialization\Accessor\AccessorInterface;
use Chubbyphp\Deserialization\Denormalizer\DenormalizerContextInterface;
use Chubbyphp\Deserialization\Denormalizer\Relation\Doctrine\ReferenceManyFieldDenormalizer;
use Chubbyphp\Deserialization\Denormalizer\DenormalizerInterface;
use Chubbyphp\Deserialization\DeserializerRuntimeException;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Persistence\Proxy;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Chubbyphp\Deserialization\Denormalizer\Relation\Doctrine\ReferenceManyFieldDenormalizer
 */
class ReferenceManyFieldDenormalizerTest extends TestCase
{
    public function testDenormalizeFieldWithoutArrayDenormalizer()
    {
        self::expectException(DeserializerRuntimeException::class);
        self::expectExceptionMessage('There is an invalid data type "double", needed "array" at path: "children"');

        $parent = $this->getParent();

        $fieldDenormalizer = new ReferenceManyFieldDenormalizer(function (string $id) {}, $this->getAccessor());
        $fieldDenormalizer->denormalizeField(
            'children',
            $parent,
            18.9,
            $this->getDenormalizerContext(),
            $this->getDenormalizer()
        );
    }

    public function testDenormalizeFieldWithArrayButNullChildDenormalizer()
    {
        self::expectException(DeserializerRuntimeException::class);
        self::expectExceptionMessage('There is an invalid data type "double", needed "string" at path: "children[0]"');

        $parent = $this->getParent();

        $fieldDenormalizer = new ReferenceManyFieldDenormalizer(function (string $id) {}, $this->getAccessor());
        $fieldDenormalizer->denormalizeField(
            'children',
            $parent,
            [18.9],
            $this->getDenormalizerContext(),
            $this->getDenormalizer()
        );
    }

    public function testDenormalizeFieldWithNull()
    {
        $parent = $this->getParent();

        $fieldDenormalizer = new ReferenceManyFieldDenormalizer(
            function (string $id) {},
            $this->getAccessor()
        );

        $fieldDenormalizer->denormalizeField(
            'children',
            $parent,
            null,
            $this->getDenormalizerContext()
        );

        self::assertNull($parent->getChildren());
    }

    public function testDenormalizeField()
    {
        $parent = $this->getParent();

        $fieldDenormalizer = new ReferenceManyFieldDenormalizer(
            function (string $id) {
                self::assertSame('60a9ee14-64d6-4992-8042-8d1528ac02d6', $id);

                return $this->getChild()->setName('php');
            },
            $this->getAccessor()
        );

        $fieldDenormalizer->denormalizeField(
            'children',
            $parent,
            ['60a9ee14-64d6-4992-8042-8d1528ac02d6'],
            $this->getDenormalizerContext(),
            $this->getDenormalizer()
        );

        self::assertSame('php', $parent->getChildren()[0]->getName());
    }

    public function testDenormalizeFieldWithProxy()
    {
        $parent = $this->getParent();

        $fieldDenormalizer = new ReferenceManyFieldDenormalizer(
            function (string $id) {
                self::assertSame('60a9ee14-64d6-4992-8042-8d1528ac02d6', $id);

                return $this->getDoctrineProxyChild()->setName('php');
            },
            $this->getAccessor()
        );

        $fieldDenormalizer->denormalizeField(
            'children',
            $parent,
            ['60a9ee14-64d6-4992-8042-8d1528ac02d6'],
            $this->getDenormalizerContext(),
            $this->getDenormalizer()
        );

        self::assertSame('php', $parent->getChildren()[0]->getName());
    }

    /**
     * @return object
     */
    private function getParent()
    {
        return new class() {
            /**
             * @var array|null
             */
            private $children;

            /**
             * @return array|null
             */
            public function getChildren()
            {
                return $this->children;
            }

            /**
             * @param Collection|null $children
             *
             * @return self
             */
            public function setChildren(Collection $children = null): self
            {
                $this->children = $children;

                return $this;
            }
        };
    }

    /**
     * @return object
     */
    private function getChild()
    {
        return new class() {
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
             *
             * @return self
             */
            public function setName(string $name): self
            {
                $this->name = $name;

                return $this;
            }
        };
    }

    /**
     * @return Proxy|MockObject
     */
    private function getDoctrineProxyChild(): Proxy
    {
        /** @var Proxy|MockObject $child */
        $child = $this->getMockBuilder(Proxy::class)
            ->setMethods(['__load', '__isInitialized', 'getName', 'setName'])
            ->getMockForAbstractClass();

        $child
            ->expects(self::once())
            ->method('__isInitialized');

        $child
            ->expects(self::once())
            ->method('__load');

        $child
            ->expects(self::once())
            ->method('setName')
            ->willReturnCallback(function ($name) use ($child) {
                $child->__name = $name;

                return $child;
            });

        $child
            ->expects(self::once())
            ->method('getName')
            ->willReturnCallback(function () use ($child) {
                return $child->__name;
            });

        return $child;
    }

    /**
     * @return AccessorInterface
     */
    private function getAccessor(): AccessorInterface
    {
        /** @var AccessorInterface|\PHPUnit_Framework_MockObject_MockObject $accessor */
        $accessor = $this->getMockBuilder(AccessorInterface::class)->getMockForAbstractClass();

        $accessor->expects(self::any())->method('setValue')->willReturnCallback(function ($object, $value) {
            $object->setChildren($value);
        });

        $accessor->expects(self::any())->method('getValue')->willReturnCallback(function ($object) {
            return $object->getChildren();
        });

        return $accessor;
    }

    /**
     * @return DenormalizerContextInterface
     */
    private function getDenormalizerContext(): DenormalizerContextInterface
    {
        /** @var DenormalizerContextInterface|\PHPUnit_Framework_MockObject_MockObject $context */
        $context = $this->getMockBuilder(DenormalizerContextInterface::class)->getMockForAbstractClass();

        return $context;
    }

    /**
     * @return DenormalizerInterface
     */
    private function getDenormalizer(): DenormalizerInterface
    {
        /** @var DenormalizerInterface|\PHPUnit_Framework_MockObject_MockObject $denormalizer */
        $denormalizer = $this->getMockBuilder(DenormalizerInterface::class)->getMockForAbstractClass();

        $denormalizer->expects(self::any())->method('denormalize')->willReturnCallback(
            function ($object, array $data, DenormalizerContextInterface $context = null, string $path = '') {
                if (is_string($object)) {
                    $object = $this->getChild();
                }

                $object->setName($data['name']);

                return $object;
            }
        );

        return $denormalizer;
    }
}
