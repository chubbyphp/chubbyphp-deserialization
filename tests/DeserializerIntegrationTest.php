<?php

declare(strict_types=1);

namespace Chubbyphp\Tests\Deserialization;

use Chubbyphp\Deserialization\Decoder\Decoder;
use Chubbyphp\Deserialization\Decoder\JsonDecoderType;
use Chubbyphp\Deserialization\Denormalizer\Denormalizer;
use Chubbyphp\Deserialization\Denormalizer\DenormalizerContextBuilder;
use Chubbyphp\Deserialization\Deserializer;
use Chubbyphp\Deserialization\DeserializerRuntimeException;
use Chubbyphp\Tests\Deserialization\Resources\Mapping\BaseChildModelMapping;
use Chubbyphp\Tests\Deserialization\Resources\Mapping\ChildModelMapping;
use Chubbyphp\Tests\Deserialization\Resources\Mapping\ParentModelMapping;
use Chubbyphp\Tests\Deserialization\Resources\Model\ChildModel;
use Chubbyphp\Tests\Deserialization\Resources\Model\ParentModel;
use PHPUnit\Framework\TestCase;

/**
 * @coversNothing
 */
class DeserializerIntegrationTest extends TestCase
{
    public function testDenormalizeByClass()
    {
        $childModelMapping = new ChildModelMapping();

        $deserializer = new Deserializer(
            new Decoder([new JsonDecoderType()]),
            new Denormalizer([
                new BaseChildModelMapping($childModelMapping, ['model']),
                $childModelMapping,
                new ParentModelMapping(),
            ])
        );

        $data = json_encode([
            'name' => 'Name',
            'children' => [
                [
                    '_type' => 'model',
                    'name' => 'Name',
                    'value' => 'Value',
                ],
            ],
        ]);

        $parentObject = $deserializer->deserialize(ParentModel::class, $data, 'application/json');

        self::assertSame('Name', $parentObject->getName());
        self::assertCount(1, $parentObject->getChildren());
        self::assertSame('Name', $parentObject->getChildren()[0]->getName());
        self::assertSame('Value', $parentObject->getChildren()[0]->getValue());
    }

    public function testDenormalizeByClassAndMissingChildType()
    {
        self::expectException(DeserializerRuntimeException::class);
        self::expectExceptionMessage('Missing object type, supported are "model" at path: "children[0]"');

        $childModelMapping = new ChildModelMapping();

        $deserializer = new Deserializer(
            new Decoder([new JsonDecoderType()]),
            new Denormalizer([
                new BaseChildModelMapping($childModelMapping, ['model']),
                $childModelMapping,
                new ParentModelMapping(),
            ])
        );

        $data = json_encode([
            'name' => 'Name',
            'children' => [
                [
                    'name' => 'Name',
                    'value' => 'Value',
                ],
            ],
        ]);

        $deserializer->deserialize(ParentModel::class, $data, 'application/json');
    }

    public function testDenormalizeByClassAndInvalidChildType()
    {
        self::expectException(DeserializerRuntimeException::class);
        self::expectExceptionMessage('Unsupported object type "unknown", supported are "model" at path: "children[0]"');

        $childModelMapping = new ChildModelMapping();

        $deserializer = new Deserializer(
            new Decoder([new JsonDecoderType()]),
            new Denormalizer([
                new BaseChildModelMapping($childModelMapping, ['model']),
                $childModelMapping,
                new ParentModelMapping(),
            ])
        );

        $data = json_encode([
            'name' => 'Name',
            'children' => [
                [
                    '_type' => 'unknown',
                    'name' => 'Name',
                    'value' => 'Value',
                ],
            ],
        ]);

        $deserializer->deserialize(ParentModel::class, $data, 'application/json');
    }

    public function testDenormalizeByObject()
    {
        $childModelMapping = new ChildModelMapping();

        $deserializer = new Deserializer(
            new Decoder([new JsonDecoderType()]),
            new Denormalizer([
                new BaseChildModelMapping($childModelMapping, ['model']),
                $childModelMapping,
                new ParentModelMapping(),
            ])
        );

        $data = json_encode([
            'name' => 'Name',
            'children' => [
                [
                    '_type' => 'model',
                    'name' => 'Name',
                    'value' => 'Value',
                ],
            ],
        ]);

        $childrenObject1 = new ChildModel();
        $childrenObject1->setName('oldName1');

        $childrenObject2 = new ChildModel();
        $childrenObject2->setName('oldNam2');

        $parentObject = new ParentModel();
        $parentObject->setName('oldName');
        $parentObject->setChildren([$childrenObject1, $childrenObject2]);

        $parentObject = $deserializer->deserialize($parentObject, $data, 'application/json');

        self::assertSame('Name', $parentObject->getName());
        self::assertCount(1, $parentObject->getChildren());
        self::assertSame('Name', $parentObject->getChildren()[0]->getName());
        self::assertSame('Value', $parentObject->getChildren()[0]->getValue());
    }

    public function testDenormalizeWithAdditionalFieldsExpectsException()
    {
        self::expectException(DeserializerRuntimeException::class);
        self::expectExceptionMessage('There are additional field(s) at paths: "unknownField"');

        $childModelMapping = new ChildModelMapping();

        $deserializer = new Deserializer(
            new Decoder([new JsonDecoderType()]),
            new Denormalizer([
                new BaseChildModelMapping($childModelMapping, ['model']),
                $childModelMapping,
                new ParentModelMapping(),
            ])
        );

        $data = json_encode(['name' => 'Name', 'unknownField' => 'value']);

        $deserializer->deserialize(ParentModel::class, $data, 'application/json');
    }

    public function testDenormalizeWithAllowedAdditionalFields()
    {
        $childModelMapping = new ChildModelMapping();

        $deserializer = new Deserializer(
            new Decoder([new JsonDecoderType()]),
            new Denormalizer([
                new BaseChildModelMapping($childModelMapping, ['model']),
                $childModelMapping,
                new ParentModelMapping(),
            ])
        );

        $data = json_encode(['name' => 'Name', 'unknownField' => 'value']);

        $object = $deserializer->deserialize(
            ParentModel::class,
            $data,
            'application/json',
            DenormalizerContextBuilder::create()->setAllowedAdditionalFields(true)->getContext()
        );

        self::assertSame('Name', $object->getName());
    }
}
