<?php

declare(strict_types=1);

namespace Chubbyphp\Tests\Deserialization\Denormalizer;

use Chubbyphp\Deserialization\Accessor\AccessorInterface;
use Chubbyphp\Deserialization\Denormalizer\DenormalizerContextInterface;
use Chubbyphp\Deserialization\Denormalizer\CollectionFieldDenormalizer;
use Chubbyphp\Deserialization\Denormalizer\DenormalizerInterface;
use Chubbyphp\Deserialization\DeserializerLogicException;
use Chubbyphp\Deserialization\DeserializerRuntimeException;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Chubbyphp\Deserialization\Denormalizer\CollectionFieldDenormalizer
 */
class CollectionFieldDenormalizerTest extends TestCase
{
    public function testDenormalizeFieldWithMissingDenormalizer()
    {
        self::expectException(DeserializerLogicException::class);
        self::expectExceptionMessage('There is no denormalizer at path: "children"');

        $parent = $this->getParent();

        $fieldDenormalizer = new CollectionFieldDenormalizer(get_class($this->getChild()), $this->getAccessor());
        $fieldDenormalizer->denormalizeField('children', $parent, [['name' => 'name']], $this->getDenormalizerContext());
    }

    public function testDenormalizeFieldWithoutArrayDenormalizer()
    {
        self::expectException(DeserializerRuntimeException::class);
        self::expectExceptionMessage('There is an invalid data type "NULL", needed "array" at path: "children"');

        $parent = $this->getParent();

        $fieldDenormalizer = new CollectionFieldDenormalizer(get_class($this->getChild()), $this->getAccessor());
        $fieldDenormalizer->denormalizeField(
            'children',
            $parent,
            null,
            $this->getDenormalizerContext(),
            $this->getDenormalizer()
        );
    }

    public function testDenormalizeFieldWithArrayButNullChildDenormalizer()
    {
        self::expectException(DeserializerRuntimeException::class);
        self::expectExceptionMessage('There is an invalid data type "NULL", needed "array" at path: "children[0]"');

        $parent = $this->getParent();

        $fieldDenormalizer = new CollectionFieldDenormalizer(get_class($this->getChild()), $this->getAccessor());
        $fieldDenormalizer->denormalizeField(
            'children',
            $parent,
            [null],
            $this->getDenormalizerContext(),
            $this->getDenormalizer()
        );
    }

    public function testDenormalizeFieldWithNewChild()
    {
        $parent = $this->getParent();

        $fieldDenormalizer = new CollectionFieldDenormalizer(get_class($this->getChild()), $this->getAccessor());
        $fieldDenormalizer->denormalizeField(
            'children',
            $parent,
            [['name' => 'name']],
            $this->getDenormalizerContext(),
            $this->getDenormalizer()
        );

        self::assertSame('name', $parent->getChildren()[0]->getName());
    }

    public function testDenormalizeFieldWithExistingChild()
    {
        $parent = $this->getParent();
        $parent->setChildren([$this->getChild()]);

        $fieldDenormalizer = new CollectionFieldDenormalizer(get_class($this->getChild()), $this->getAccessor());
        $fieldDenormalizer->denormalizeField(
            'children',
            $parent,
            [['name' => 'name']],
            $this->getDenormalizerContext(),
            $this->getDenormalizer()
        );

        self::assertSame('name', $parent->getChildren()[0]->getName());
    }

    /**
     * @return object
     */
    private function getParent()
    {
        return new class() {
            /**
             * @var array
             */
            private $children = [];

            /**
             * @return array
             */
            public function getChildren(): array
            {
                return $this->children;
            }

            /**
             * @param array $children
             *
             * @return self
             */
            public function setChildren(array $children): self
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
