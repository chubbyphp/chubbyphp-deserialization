<?php

declare(strict_types=1);

namespace Chubbyphp\Tests\Deserialization;

use Chubbyphp\Deserialization\Decoder\Decoder;
use Chubbyphp\Deserialization\Decoder\JsonDecoderType;
use Chubbyphp\Deserialization\Denormalizer\Denormalizer;
use Chubbyphp\Deserialization\Denormalizer\DenormalizerException;
use Chubbyphp\Deserialization\Denormalizer\DenormalizingContextBuilder;
use Chubbyphp\Deserialization\Deserializer;
use Chubbyphp\Deserialization\Mapping\DenormalizingFieldMappingBuilder;
use Chubbyphp\Deserialization\Mapping\DenormalizingObjectMappingInterface;
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
            new Denormalizer([$this->getObjectMapping()])
        );

        $data = json_encode(['name' => 'Name']);

        $object = $deserializer->deserialize(get_class($this->getObject()), $data, 'application/json');

        self::assertSame('Name', $object->getName());
    }

    public function testDenormalizeByObject()
    {
        $deserializer = new Deserializer(
            new Decoder([new JsonDecoderType()]),
            new Denormalizer([$this->getObjectMapping()])
        );

        $data = json_encode(['name' => 'Name']);

        $object = $deserializer->deserialize($this->getObject(), $data, 'application/json');

        self::assertSame('Name', $object->getName());
    }

    public function testDenormalizeWithAdditionalFieldsExpectsException()
    {
        self::expectException(DenormalizerException::class);
        self::expectExceptionMessage('There are additional field(s) at paths: unknownField');

        $deserializer = new Deserializer(
            new Decoder([new JsonDecoderType()]),
            new Denormalizer([$this->getObjectMapping()])
        );

        $data = json_encode(['name' => 'Name', 'unknownField' => 'value']);

        $deserializer->deserialize($this->getObject(), $data, 'application/json');
    }

    public function testDenormalizeWithAllowedAdditionalFields()
    {
        $deserializer = new Deserializer(
            new Decoder([new JsonDecoderType()]),
            new Denormalizer([$this->getObjectMapping()])
        );

        $data = json_encode(['name' => 'Name', 'unknownField' => 'value']);

        $object = $deserializer->deserialize(
            $this->getObject(),
            $data,
            'application/json',
            DenormalizingContextBuilder::create()->setAllowedAdditionalFields(true)->getContext()
        );

        self::assertSame('Name', $object->getName());
    }

    /**
     * @return object
     */
    public function getObject()
    {
        return new class() {
            /**
             * @var string|null
             */
            private $name;

            /**
             * @param string $name
             */
            public function setName(string $name)
            {
                $this->name = $name;
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
    private function getObjectMapping()
    {
        return new class($this) implements DenormalizingObjectMappingInterface {
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
                return get_class($this->test->getObject());
            }

            /**
             * @param string|null $type
             *
             * @return callable
             */
            public function getFactory(string $type = null): callable
            {
                return function () {
                    return $this->test->getObject();
                };
            }

            public function getDenormalizingFieldMappings(): array
            {
                return [
                    DenormalizingFieldMappingBuilder::create('name')->getMapping(),
                ];
            }
        };
    }
}
