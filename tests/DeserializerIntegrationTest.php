<?php

declare(strict_types=1);

namespace Chubbyphp\Tests\Deserialization;

use Chubbyphp\Deserialization\Decoder\Decoder;
use Chubbyphp\Deserialization\Decoder\JsonDecoderType;
use Chubbyphp\Deserialization\Denormalizer\Denormalizer;
use Chubbyphp\Deserialization\Denormalizer\DenormalizerContext;
use Chubbyphp\Deserialization\Denormalizer\DenormalizerException;
use Chubbyphp\Deserialization\Deserializer;
use Chubbyphp\Deserialization\Mapping\DenormalizingFieldMapping;
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
            new Denormalizer([$this->getModelMapping()])
        );

        $data = json_encode(['name' => 'Dominik']);

        $model = $deserializer->deserialize(get_class($this->getModel()), $data, 'application/json');

        self::assertSame('Dominik', $model->getName());
    }

    public function testDenormalizeByObject()
    {
        $deserializer = new Deserializer(
            new Decoder([new JsonDecoderType()]),
            new Denormalizer([$this->getModelMapping()])
        );

        $data = json_encode(['name' => 'Dominik']);

        $model = $deserializer->deserialize($this->getModel(), $data, 'application/json');

        self::assertSame('Dominik', $model->getName());
    }

    public function testDenormalizeWithAdditionalFieldsExpectsException()
    {
        self::expectException(DenormalizerException::class);
        self::expectExceptionMessage('There is an additional field at path: unknownField');

        $deserializer = new Deserializer(
            new Decoder([new JsonDecoderType()]),
            new Denormalizer([$this->getModelMapping()])
        );

        $data = json_encode(['name' => 'Dominik', 'unknownField' => 'value']);

        $deserializer->deserialize($this->getModel(), $data, 'application/json');
    }

    public function testDenormalizeWithAllowedAdditionalFields()
    {
        $deserializer = new Deserializer(
            new Decoder([new JsonDecoderType()]),
            new Denormalizer([$this->getModelMapping()])
        );

        $data = json_encode(['name' => 'Dominik', 'unknownField' => 'value']);

        $model = $deserializer->deserialize($this->getModel(), $data, 'application/json', new DenormalizerContext(true));

        self::assertSame('Dominik', $model->getName());
    }

    /**
     * @return object
     */
    public function getModel()
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
        };
    }

    /**
     * @return object
     */
    private function getModelMapping()
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
                return get_class($this->test->getModel());
            }

            /**
             * @return callable
             */
            public function getFactory(): callable
            {
                return function () {
                    return $this->test->getModel();
                };
            }

            public function getDenormalizingFieldMappings(): array
            {
                return [
                    new DenormalizingFieldMapping('name'),
                ];
            }
        };
    }
}
