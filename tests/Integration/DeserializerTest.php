<?php

declare(strict_types=1);

namespace Chubbyphp\Tests\Deserialization\Integration;

use Chubbyphp\Deserialization\Decoder\Decoder;
use Chubbyphp\Deserialization\Decoder\JsonTypeDecoder;
use Chubbyphp\Deserialization\Denormalizer\Denormalizer;
use Chubbyphp\Deserialization\Denormalizer\DenormalizerContextBuilder;
use Chubbyphp\Deserialization\Denormalizer\DenormalizerObjectMappingRegistry;
use Chubbyphp\Deserialization\Deserializer;
use Chubbyphp\Deserialization\DeserializerRuntimeException;
use Chubbyphp\Tests\Deserialization\Resources\Mapping\BaseManyModelMapping;
use Chubbyphp\Tests\Deserialization\Resources\Mapping\ManyModelMapping;
use Chubbyphp\Tests\Deserialization\Resources\Mapping\ModelMapping;
use Chubbyphp\Tests\Deserialization\Resources\Mapping\OneModelMapping;
use Chubbyphp\Tests\Deserialization\Resources\Model\ManyModel;
use Chubbyphp\Tests\Deserialization\Resources\Model\Model;
use Chubbyphp\Tests\Deserialization\Resources\Model\OneModel;
use PHPUnit\Framework\TestCase;
use Psr\Log\AbstractLogger;

/**
 * @coversNothing
 *
 * @internal
 */
final class DeserializerTest extends TestCase
{
    public function testDenormalizeByClass(): void
    {
        $childModelMapping = new ManyModelMapping();

        $logger = $this->getLogger();

        $deserializer = new Deserializer(
            new Decoder([new JsonTypeDecoder()]),
            new Denormalizer(
                new DenormalizerObjectMappingRegistry([
                    new BaseManyModelMapping($childModelMapping, ['many-model']),
                    $childModelMapping,
                    new ModelMapping(),
                    new OneModelMapping(),
                ]),
                $logger
            )
        );

        $data = json_encode([
            'name' => 'Name',
            'one' => [
                'name' => 'Name',
                'value' => 'Value',
            ],
            'manies' => [
                [
                    '_type' => 'many-model',
                    'name' => 'Name',
                    'value' => 'Value',
                ],
            ],
        ]);

        $model = $deserializer->deserialize(Model::class, $data, 'application/json');

        self::assertSame('Name', $model->getName());
        self::assertInstanceOf(OneModel::class, $model->getOne());
        self::assertSame('Name', $model->getOne()->getName());
        self::assertSame('Value', $model->getOne()->getValue());
        self::assertCount(1, $model->getManies());
        self::assertInstanceOf(ManyModel::class, $model->getManies()[0]);
        self::assertSame('Name', $model->getManies()[0]->getName());
        self::assertSame('Value', $model->getManies()[0]->getValue());

        self::assertEquals(
            [
                [
                    'level' => 'info',
                    'message' => 'deserialize: path {path}',
                    'context' => [
                        'path' => 'name',
                    ],
                ],
                [
                    'level' => 'info',
                    'message' => 'deserialize: path {path}',
                    'context' => [
                        'path' => 'one',
                    ],
                ],
                [
                    'level' => 'info',
                    'message' => 'deserialize: path {path}',
                    'context' => [
                        'path' => 'one.name',
                    ],
                ],
                [
                    'level' => 'info',
                    'message' => 'deserialize: path {path}',
                    'context' => [
                        'path' => 'one.value',
                    ],
                ],
                [
                    'level' => 'info',
                    'message' => 'deserialize: path {path}',
                    'context' => [
                        'path' => 'manies',
                    ],
                ],
                [
                    'level' => 'info',
                    'message' => 'deserialize: path {path}',
                    'context' => [
                        'path' => 'manies[0].name',
                    ],
                ],
                [
                    'level' => 'info',
                    'message' => 'deserialize: path {path}',
                    'context' => [
                        'path' => 'manies[0].value',
                    ],
                ],
            ],
            $logger->getEntries()
        );
    }

    public function testDenormalizeByClassAndMissingChildType(): void
    {
        $this->expectException(DeserializerRuntimeException::class);
        $this->expectExceptionMessage('Missing object type, supported are "many-model" at path: "manies[0]"');

        $childModelMapping = new ManyModelMapping();

        $deserializer = new Deserializer(
            new Decoder([new JsonTypeDecoder()]),
            new Denormalizer(
                new DenormalizerObjectMappingRegistry([
                    new BaseManyModelMapping($childModelMapping, ['many-model']),
                    $childModelMapping,
                    new ModelMapping(),
                ])
            )
        );

        $data = json_encode([
            'name' => 'Name',
            'manies' => [
                [
                    'name' => 'Name',
                    'value' => 'Value',
                ],
            ],
        ]);

        $deserializer->deserialize(Model::class, $data, 'application/json');
    }

    public function testDenormalizeByClassAndInvalidChildType(): void
    {
        $this->expectException(DeserializerRuntimeException::class);
        $this->expectExceptionMessage('Unsupported object type "unknown", supported are "many-model" at path: "manies[0]"');

        $childModelMapping = new ManyModelMapping();

        $deserializer = new Deserializer(
            new Decoder([new JsonTypeDecoder()]),
            new Denormalizer(
                new DenormalizerObjectMappingRegistry([
                    new BaseManyModelMapping($childModelMapping, ['many-model']),
                    $childModelMapping,
                    new ModelMapping(),
                ])
            )
        );

        $data = json_encode([
            'name' => 'Name',
            'manies' => [
                [
                    '_type' => 'unknown',
                    'name' => 'Name',
                    'value' => 'Value',
                ],
            ],
        ]);

        $deserializer->deserialize(Model::class, $data, 'application/json');
    }

    public function testDenormalizeByObject(): void
    {
        $childModelMapping = new ManyModelMapping();

        $logger = $this->getLogger();

        $deserializer = new Deserializer(
            new Decoder([new JsonTypeDecoder()]),
            new Denormalizer(
                new DenormalizerObjectMappingRegistry([
                    new BaseManyModelMapping($childModelMapping, ['many-model']),
                    $childModelMapping,
                    new ModelMapping(),
                    new OneModelMapping(),
                ]),
                $logger
            )
        );

        $data = json_encode([
            'name' => 'Name',
            'one' => [
                'name' => 'Name',
            ],
            'manies' => [
                [
                    '_type' => 'many-model',
                    'name' => 'Name',
                    'value' => 'Value',
                ],
            ],
        ]);

        $oneModel = new OneModel();
        $oneModel->setValue('Value');

        $manyModel1 = new ManyModel();
        $manyModel1->setName('oldName1');

        $manyModel2 = new ManyModel();
        $manyModel2->setName('oldNam2');

        $model = new Model();
        $model->setName('oldName');
        $model->setOne($oneModel);
        $model->setManies([$manyModel1, $manyModel2]);

        $model = $deserializer->deserialize($model, $data, 'application/json');

        self::assertSame('Name', $model->getName());
        self::assertSame($oneModel, $model->getOne());
        self::assertSame('Name', $model->getOne()->getName());
        self::assertSame('Value', $model->getOne()->getValue());
        self::assertCount(1, $model->getManies());
        self::assertSame($manyModel1, $model->getManies()[0]);
        self::assertSame('Name', $model->getManies()[0]->getName());
        self::assertSame('Value', $model->getManies()[0]->getValue());

        self::assertEquals(
            [
                [
                    'level' => 'info',
                    'message' => 'deserialize: path {path}',
                    'context' => [
                        'path' => 'name',
                    ],
                ],
                [
                    'level' => 'info',
                    'message' => 'deserialize: path {path}',
                    'context' => [
                        'path' => 'one',
                    ],
                ],
                [
                    'level' => 'info',
                    'message' => 'deserialize: path {path}',
                    'context' => [
                        'path' => 'one.name',
                    ],
                ],
                [
                    'level' => 'info',
                    'message' => 'deserialize: path {path}',
                    'context' => [
                        'path' => 'manies',
                    ],
                ],
                [
                    'level' => 'info',
                    'message' => 'deserialize: path {path}',
                    'context' => [
                        'path' => 'manies[0].name',
                    ],
                ],
                [
                    'level' => 'info',
                    'message' => 'deserialize: path {path}',
                    'context' => [
                        'path' => 'manies[0].value',
                    ],
                ],
            ],
            $logger->getEntries()
        );
    }

    public function testDenormalizeByObjectClearMissing(): void
    {
        $childModelMapping = new ManyModelMapping();

        $logger = $this->getLogger();

        $deserializer = new Deserializer(
            new Decoder([new JsonTypeDecoder()]),
            new Denormalizer(
                new DenormalizerObjectMappingRegistry([
                    new BaseManyModelMapping($childModelMapping, ['many-model']),
                    $childModelMapping,
                    new ModelMapping(),
                    new OneModelMapping(),
                ]),
                $logger
            )
        );

        $data = json_encode([
            'name' => 'Name',
            'one' => [
                'name' => 'Name',
            ],
            'manies' => [
                [
                    '_type' => 'many-model',
                    'name' => 'Name',
                    'value' => 'Value',
                ],
            ],
        ]);

        $oneModel = new OneModel();
        $oneModel->setValue('Value');

        $manyModel1 = new ManyModel();
        $manyModel1->setName('oldName1');

        $manyModel2 = new ManyModel();
        $manyModel2->setName('oldNam2');

        $model = new Model();
        $model->setName('oldName');
        $model->setOne($oneModel);
        $model->setManies([$manyModel1, $manyModel2]);

        $context = DenormalizerContextBuilder::create()->setClearMissing(true)->getContext();

        $model = $deserializer->deserialize($model, $data, 'application/json', $context);

        self::assertSame('Name', $model->getName());
        self::assertSame($oneModel, $model->getOne());
        self::assertSame('Name', $model->getOne()->getName());
        self::assertNull($model->getOne()->getValue());
        self::assertCount(1, $model->getManies());
        self::assertSame($manyModel1, $model->getManies()[0]);
        self::assertSame('Name', $model->getManies()[0]->getName());
        self::assertSame('Value', $model->getManies()[0]->getValue());

        self::assertEquals(
            [
                [
                    'level' => 'info',
                    'message' => 'deserialize: path {path}',
                    'context' => [
                        'path' => 'name',
                    ],
                ],
                [
                    'level' => 'info',
                    'message' => 'deserialize: path {path}',
                    'context' => [
                        'path' => 'one',
                    ],
                ],
                [
                    'level' => 'info',
                    'message' => 'deserialize: path {path}',
                    'context' => [
                        'path' => 'one.name',
                    ],
                ],
                [
                    'level' => 'info',
                    'message' => 'deserialize: path {path}',
                    'context' => [
                        'path' => 'one.value',
                    ],
                ],
                [
                    'level' => 'info',
                    'message' => 'deserialize: path {path}',
                    'context' => [
                        'path' => 'manies',
                    ],
                ],
                [
                    'level' => 'info',
                    'message' => 'deserialize: path {path}',
                    'context' => [
                        'path' => 'manies[0].name',
                    ],
                ],
                [
                    'level' => 'info',
                    'message' => 'deserialize: path {path}',
                    'context' => [
                        'path' => 'manies[0].value',
                    ],
                ],
            ],
            $logger->getEntries()
        );
    }

    public function testDenormalizeWithAdditionalFieldsExpectsException(): void
    {
        $this->expectException(DeserializerRuntimeException::class);
        $this->expectExceptionMessage('There are additional field(s) at paths: "unknownField"');

        $childModelMapping = new ManyModelMapping();

        $deserializer = new Deserializer(
            new Decoder([new JsonTypeDecoder()]),
            new Denormalizer(
                new DenormalizerObjectMappingRegistry([
                    new BaseManyModelMapping($childModelMapping, ['many-model']),
                    $childModelMapping,
                    new ModelMapping(),
                ])
            )
        );

        $data = json_encode(['name' => 'Name', 'unknownField' => 'value']);

        $deserializer->deserialize(
            Model::class,
            $data,
            'application/json',
            DenormalizerContextBuilder::create()->setAllowedAdditionalFields([])->getContext()
        );
    }

    public function testDenormalizeWithKeyCastToIntegerAdditionalFieldsExpectsException(): void
    {
        $this->expectException(DeserializerRuntimeException::class);
        $this->expectExceptionMessage('There are additional field(s) at paths: "1"');

        $childModelMapping = new ManyModelMapping();

        $deserializer = new Deserializer(
            new Decoder([new JsonTypeDecoder()]),
            new Denormalizer(
                new DenormalizerObjectMappingRegistry([
                    new BaseManyModelMapping($childModelMapping, ['many-model']),
                    $childModelMapping,
                    new ModelMapping(),
                ])
            )
        );

        $data = json_encode(['name' => 'Name', '1' => 'value']);

        $deserializer->deserialize(
            Model::class,
            $data,
            'application/json',
            DenormalizerContextBuilder::create()->setAllowedAdditionalFields([])->getContext()
        );
    }

    public function testDenormalizeWithAllowedAdditionalFields(): void
    {
        $childModelMapping = new ManyModelMapping();

        $deserializer = new Deserializer(
            new Decoder([new JsonTypeDecoder()]),
            new Denormalizer(
                new DenormalizerObjectMappingRegistry([
                    new BaseManyModelMapping($childModelMapping, ['many-model']),
                    $childModelMapping,
                    new ModelMapping(),
                ])
            )
        );

        $data = json_encode(['name' => 'Name', 'unknownField' => 'value']);

        $object = $deserializer->deserialize(
            Model::class,
            $data,
            'application/json'
        );

        self::assertSame('Name', $object->getName());
    }

    private function getLogger(): AbstractLogger
    {
        return new class() extends AbstractLogger {
            private array $entries = [];

            public function log($level, $message, array $context = []): void
            {
                $this->entries[] = ['level' => $level, 'message' => $message, 'context' => $context];
            }

            public function getEntries(): array
            {
                return $this->entries;
            }
        };
    }
}
