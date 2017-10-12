<?php

declare(strict_types=1);

namespace Chubbyphp\Tests\Deserialization;

use Chubbyphp\Deserialization\Accessor\PropertyAccessor;
use Chubbyphp\Deserialization\Decoder\Decoder;
use Chubbyphp\Deserialization\Decoder\JsonDecoderType;
use Chubbyphp\Deserialization\Denormalizer\CollectionFieldDenormalizer;
use Chubbyphp\Deserialization\Denormalizer\Denormalizer;
use Chubbyphp\Deserialization\Denormalizer\DenormalizerContextBuilder;
use Chubbyphp\Deserialization\Deserializer;
use Chubbyphp\Deserialization\DeserializerRuntimeException;
use Chubbyphp\Deserialization\Mapping\DenormalizationFieldMappingBuilder;
use Chubbyphp\Deserialization\Mapping\DenormalizationObjectMappingInterface;
use PHPUnit\Framework\TestCase;

/**
 * @coversNothing
 */
class DeserializerIntegrationTest extends TestCase
{
    public function testDenormalizeByClass()
    {
        $deserializer = new Deserializer(
            new Decoder([new JsonDecoderType()]),
            new Denormalizer([$this->getChildObjectMapping(), $this->getParentObjectMapping()])
        );

        $data = json_encode(['name' => 'Name', 'children' => [['name' => 'Name']]]);

        $parentObject = $deserializer->deserialize(get_class($this->getParentObject()), $data, 'application/json');

        self::assertSame('Name', $parentObject->getName());
        self::assertCount(1, $parentObject->getChildren());
        self::assertSame('Name', $parentObject->getChildren()[0]->getName());
    }

    public function testDenormalizeByObject()
    {
        $deserializer = new Deserializer(
            new Decoder([new JsonDecoderType()]),
            new Denormalizer([$this->getChildObjectMapping(), $this->getParentObjectMapping()])
        );

        $data = json_encode(['name' => 'Name', 'children' => [['name' => 'Name']]]);

        $childrenObject1 = $this->getChildObject();
        $childrenObject1->setName('oldName1');

        $childrenObject2 = $this->getChildObject();
        $childrenObject2->setName('oldNam2');

        $parentObject = $this->getParentObject();
        $parentObject->setName('oldName');
        $parentObject->setChildren([$childrenObject1, $childrenObject2]);

        $parentObject = $deserializer->deserialize($this->getParentObject(), $data, 'application/json');

        self::assertSame('Name', $parentObject->getName());
        self::assertCount(1, $parentObject->getChildren());
        self::assertSame('Name', $parentObject->getChildren()[0]->getName());
    }

    public function testDenormalizeWithAdditionalFieldsExpectsException()
    {
        self::expectException(DeserializerRuntimeException::class);
        self::expectExceptionMessage('There are additional field(s) at paths: "unknownField"');

        $deserializer = new Deserializer(
            new Decoder([new JsonDecoderType()]),
            new Denormalizer([$this->getChildObjectMapping(), $this->getParentObjectMapping()])
        );

        $data = json_encode(['name' => 'Name', 'unknownField' => 'value']);

        $deserializer->deserialize($this->getParentObject(), $data, 'application/json');
    }

    public function testDenormalizeWithAllowedAdditionalFields()
    {
        $deserializer = new Deserializer(
            new Decoder([new JsonDecoderType()]),
            new Denormalizer([$this->getChildObjectMapping(), $this->getParentObjectMapping()])
        );

        $data = json_encode(['name' => 'Name', 'unknownField' => 'value']);

        $object = $deserializer->deserialize(
            $this->getParentObject(),
            $data,
            'application/json',
            DenormalizerContextBuilder::create()->setAllowedAdditionalFields(true)->getContext()
        );

        self::assertSame('Name', $object->getName());
    }

    /**
     * @return object
     */
    public function getChildObject()
    {
        return new class() {
            /**
             * @var string|null
             */
            private $name;

            /**
             * @param null|string $name
             *
             * @return self
             */
            public function setName(string $name = null): self
            {
                $this->name = $name;

                return $this;
            }

            /**
             * @return string|null
             */
            public function getName()
            {
                return $this->name;
            }
        };
    }

    /**
     * @return object
     */
    public function getParentObject()
    {
        return new class() {
            /**
             * @var string|null
             */
            private $name;

            /**
             * @var object[]
             */
            private $children;

            /**
             * @param null|string $name
             *
             * @return self
             */
            public function setName(string $name = null): self
            {
                $this->name = $name;

                return $this;
            }

            /**
             * @return string|null
             */
            public function getName()
            {
                return $this->name;
            }

            /**
             * @return object[]
             */
            public function getChildren(): array
            {
                return $this->children;
            }

            /**
             * @param object[] $children
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
    private function getChildObjectMapping()
    {
        return new class($this) implements DenormalizationObjectMappingInterface {
            /**
             * @var TestCase
             */
            private $test;

            /**
             * @param DeserializerIntegrationTest $test
             */
            public function __construct(DeserializerIntegrationTest $test)
            {
                $this->test = $test;
            }

            /**
             * @return string
             */
            public function getClass(): string
            {
                return get_class($this->test->getChildObject());
            }

            /**
             * @param string      $path
             * @param string|null $type
             *
             * @return callable
             */
            public function getDenormalizationFactory(string $path, string $type = null): callable
            {
                return function () {
                    return $this->test->getChildObject();
                };
            }

            /**
             * @param string      $path
             * @param string|null $type
             *
             * @return array
             */
            public function getDenormalizationFieldMappings(string $path, string $type = null): array
            {
                return [
                    DenormalizationFieldMappingBuilder::create('name')->getMapping(),
                ];
            }
        };
    }

    /**
     * @return object
     */
    private function getParentObjectMapping()
    {
        return new class($this) implements DenormalizationObjectMappingInterface {
            /**
             * @var TestCase
             */
            private $test;

            /**
             * @param DeserializerIntegrationTest $test
             */
            public function __construct(DeserializerIntegrationTest $test)
            {
                $this->test = $test;
            }

            /**
             * @return string
             */
            public function getClass(): string
            {
                return get_class($this->test->getParentObject());
            }

            /**
             * @param string $class
             *
             * @return bool
             */
            public function isDenormalizationResponsible(string $class): bool
            {
                return get_class($this->test->getParentObject()) === $class;
            }

            /**
             * @param string      $path
             * @param string|null $type
             *
             * @return callable
             */
            public function getDenormalizationFactory(string $path, string $type = null): callable
            {
                return function () {
                    return $this->test->getParentObject();
                };
            }

            /**
             * @param string      $path
             * @param string|null $type
             *
             * @return array
             */
            public function getDenormalizationFieldMappings(string $path, string $type = null): array
            {
                return [
                    DenormalizationFieldMappingBuilder::create('name')->getMapping(),
                    DenormalizationFieldMappingBuilder::create('children')->setFieldDenormalizer(
                        new CollectionFieldDenormalizer(
                            get_class($this->test->getChildObject()),
                            new PropertyAccessor('children')
                        )
                    )->getMapping(),
                ];
            }
        };
    }
}
