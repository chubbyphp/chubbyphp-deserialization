<?php

declare(strict_types=1);

namespace Chubbyphp\Tests\Deserialization\Denormalizer;

use Chubbyphp\Deserialization\Denormalizer\DenormalizerContextInterface;
use Chubbyphp\Deserialization\Denormalizer\DenormalizerInterface;
use Chubbyphp\Deserialization\Denormalizer\ReferenceFieldDenormalizer;
use Chubbyphp\Deserialization\Accessor\AccessorInterface;
use Chubbyphp\Deserialization\DeserializerLogicException;
use Chubbyphp\Deserialization\DeserializerRuntimeException;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Chubbyphp\Deserialization\Denormalizer\ReferenceFieldDenormalizer
 */
class ReferenceFieldDenormalizerTest extends TestCase
{
    public function testDenormalizeFieldWithMissingDenormalizer()
    {
        self::expectException(DeserializerLogicException::class);
        self::expectExceptionMessage('There is no denormalizer at path: "reference"');

        $object = $this->getObject();

        $fieldDenormalizer = new ReferenceFieldDenormalizer(
            get_class($this->getReference()),
            function (string $class, string $id) {},
            $this->getAccessor()
        );

        $fieldDenormalizer->denormalizeField(
            'reference',
            $object,
            ['name' => 'name'],
            $this->getDenormalizerContext()
        );
    }

    public function testDenormalizeFieldWithArray()
    {
        $fieldDenormalizer = new ReferenceFieldDenormalizer(
            get_class($this->getReference()),
            function (string $class, string $id) {},
            $this->getAccessor()
        );

        $object = $this->getObject();

        $fieldDenormalizer->denormalizeField(
            'reference',
            $object,
            ['name' => 'php'],
            $this->getDenormalizerContext(),
            $this->getDenormalizer()
        );

        $reference = $object->getReference();

        self::assertInstanceOf(get_class($this->getReference()), $reference);
        self::assertSame('php', $reference->getName());
    }

    public function testDenormalizeFieldWithString()
    {
        $fieldDenormalizer = new ReferenceFieldDenormalizer(
            get_class($this->getReference()),
            function (string $class, string $id) {
                self::assertSame(get_class($this->getReference()), $class);
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

    public function testDenormalizeFieldWithStringAndWithoutRepository()
    {
        self::expectException(DeserializerRuntimeException::class);
        self::expectExceptionMessage('There is an invalid data type "string", needed "array" at path: "reference"');

        $fieldDenormalizer = new ReferenceFieldDenormalizer(
            get_class($this->getReference()),
            null,
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
    }

    public function testDenormalizeFieldWithWrongType()
    {
        self::expectException(DeserializerRuntimeException::class);
        self::expectExceptionMessage('There is an invalid data type "integer", needed "array|string" at path: "reference"');

        $fieldDenormalizer = new ReferenceFieldDenormalizer(
            get_class($this->getReference()),
            function (string $class, string $id) {},
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
}
