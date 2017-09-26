<?php

declare(strict_types=1);

namespace Chubbyphp\Tests\Deserialization;

use Chubbyphp\Deserialization\Decoder\Decoder;
use Chubbyphp\Deserialization\Decoder\JsonDecoderType;
use Chubbyphp\Deserialization\Denormalizer\Denormalizer;
use Chubbyphp\Deserialization\Denormalizer\DenormalizerContext;
use Chubbyphp\Deserialization\Denormalizer\DenormalizerException;
use Chubbyphp\Deserialization\Deserializer;
use Chubbyphp\Tests\Deserialization\Resources\Mapping\DenormalizationModelMapping;
use Chubbyphp\Tests\Deserialization\Resources\Model\Model;
use PHPUnit\Framework\TestCase;

class DeserializerTest extends TestCase
{
    public function testDenormalizeByClass()
    {
        $deserializer = new Deserializer(
            new Decoder([new JsonDecoderType()]),
            new Denormalizer([new DenormalizationModelMapping()])
        );

        $data = json_encode(['name' => 'Dominik']);

        $model = $deserializer->deserialize(Model::class, $data, 'application/json');

        self::assertSame('Dominik', $model->getName());
    }

    public function testDenormalizeByObject()
    {
        $deserializer = new Deserializer(
            new Decoder([new JsonDecoderType()]),
            new Denormalizer([new DenormalizationModelMapping()])
        );

        $data = json_encode(['name' => 'Dominik']);

        $model = $deserializer->deserialize(new Model(), $data, 'application/json');

        self::assertSame('Dominik', $model->getName());
    }

    public function testDenormalizeWithAdditionalFieldsExpectsException()
    {
        self::expectException(DenormalizerException::class);
        self::expectExceptionMessage('There is an additional field at path: unknownField');

        $deserializer = new Deserializer(
            new Decoder([new JsonDecoderType()]),
            new Denormalizer([new DenormalizationModelMapping()])
        );

        $data = json_encode(['name' => 'Dominik', 'unknownField' => 'value']);

        $deserializer->deserialize(new Model(), $data, 'application/json');
    }

    public function testDenormalizeWithAllowedAdditionalFields()
    {
        $deserializer = new Deserializer(
            new Decoder([new JsonDecoderType()]),
            new Denormalizer([new DenormalizationModelMapping()])
        );

        $data = json_encode(['name' => 'Dominik', 'unknownField' => 'value']);

        $model = $deserializer->deserialize(new Model(), $data, 'application/json', new DenormalizerContext(true));

        self::assertSame('Dominik', $model->getName());
    }
}
