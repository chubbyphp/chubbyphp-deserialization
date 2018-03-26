<?php

declare(strict_types=1);

namespace Chubbyphp\Tests\Deserialization\Denormalizer\Relation\Doctrine;

use Chubbyphp\Deserialization\Denormalizer\DenormalizerContextInterface;
use Chubbyphp\Deserialization\Denormalizer\DenormalizerInterface;
use Chubbyphp\Deserialization\Denormalizer\Relation\Doctrine\ReferenceOneFieldDenormalizer;
use Chubbyphp\Deserialization\Accessor\AccessorInterface;
use Chubbyphp\Deserialization\DeserializerRuntimeException;
use Doctrine\Common\Persistence\Proxy;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Chubbyphp\Deserialization\Denormalizer\Relation\Doctrine\ReferenceOneFieldDenormalizer
 */
class ReferenceOneFieldDenormalizerTest extends TestCase
{
    public function testDenormalizeFieldWithNull()
    {
        $fieldDenormalizer = new ReferenceOneFieldDenormalizer(
            function (string $id) {},
            $this->getAccessor()
        );

        $object = $this->getObject();

        $fieldDenormalizer->denormalizeField(
            'reference',
            $object,
            null,
            $this->getDenormalizerContext()
        );

        self::assertNull($object->getReference());
    }

    public function testDenormalizeField()
    {
        $fieldDenormalizer = new ReferenceOneFieldDenormalizer(
            function (string $id) {
                self::assertSame('60a9ee14-64d6-4992-8042-8d1528ac02d6', $id);

                return $this->getReference()->setName('php');
            },
            $this->getAccessor()
        );

        $object = $this->getObject();

        $fieldDenormalizer->denormalizeField(
            'reference',
            $object,
            '60a9ee14-64d6-4992-8042-8d1528ac02d6',
            $this->getDenormalizerContext(),
            $this->getDenormalizer()
        );

        $reference = $object->getReference();

        self::assertInstanceOf(get_class($this->getReference()), $reference);
        self::assertSame('php', $reference->getName());
    }

    public function testDenormalizeFieldWithProxy()
    {
        $fieldDenormalizer = new ReferenceOneFieldDenormalizer(
            function (string $id) {
                self::assertSame('60a9ee14-64d6-4992-8042-8d1528ac02d6', $id);

                return $this->getDoctrineProxyReference()->setName('php');
            },
            $this->getAccessor()
        );

        $object = $this->getObject();

        $fieldDenormalizer->denormalizeField(
            'reference',
            $object,
            '60a9ee14-64d6-4992-8042-8d1528ac02d6',
            $this->getDenormalizerContext(),
            $this->getDenormalizer()
        );

        $reference = $object->getReference();

        self::assertInstanceOf(Proxy::class, $reference);
        self::assertSame('php', $reference->getName());
    }

    public function testDenormalizeFieldWithWrongType()
    {
        self::expectException(DeserializerRuntimeException::class);
        self::expectExceptionMessage('There is an invalid data type "integer", needed "string" at path: "reference"');

        $fieldDenormalizer = new ReferenceOneFieldDenormalizer(
            function (string $id) {},
            $this->getAccessor()
        );

        $object = $this->getObject();

        $fieldDenormalizer->denormalizeField(
            'reference',
            $object,
            5,
            $this->getDenormalizerContext(),
            $this->getDenormalizer()
        );
    }

    /**
     * @return AccessorInterface
     */
    private function getAccessor(): AccessorInterface
    {
        /** @var AccessorInterface|\PHPUnit_Framework_MockObject_MockObject $accessor */
        $accessor = $this->getMockBuilder(AccessorInterface::class)->getMockForAbstractClass();

        $accessor->expects(self::any())->method('setValue')->willReturnCallback(function ($object, $value) {
            $object->setReference($value);
        });

        $accessor->expects(self::any())->method('getValue')->willReturnCallback(function ($object) {
            return $object->getReference();
        });

        return $accessor;
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
                    $object = $this->getReference();
                }

                $object->setName($data['name']);

                return $object;
            }
        );

        return $denormalizer;
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
     * @return object
     */
    private function getObject()
    {
        return new class() {
            /**
             * @var object
             */
            private $reference;

            /**
             * @return object
             */
            public function getReference()
            {
                return $this->reference;
            }

            /**
             * @param object $reference
             *
             * @return self
             */
            public function setReference($reference): self
            {
                $this->reference = $reference;

                return $this;
            }
        };
    }

    /**
     * @return object
     */
    private function getReference()
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
    private function getDoctrineProxyReference(): Proxy
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
}
