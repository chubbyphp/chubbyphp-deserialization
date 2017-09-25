<?php

declare(strict_types=1);

namespace Chubbyphp\Tests\Deserialization;

use Chubbyphp\Deserialization\Denormalizer\Denormalizer;
use Chubbyphp\Deserialization\Denormalizer\DenormalizerContext;
use Chubbyphp\Deserialization\Denormalizer\DenormalizerException;
use Chubbyphp\Tests\Deserialization\Resources\Mapping\DenormalizationModelMapping;
use Chubbyphp\Tests\Deserialization\Resources\Model\Model;
use PHPUnit\Framework\TestCase;

class DenormalizerTest extends TestCase
{
    public function testDenormalizeByClass()
    {
        $denormalizer = new Denormalizer([
            new DenormalizationModelMapping(),
        ]);

        $data = ['name' => 'Dominik'];

        $model = $denormalizer->denormalize(Model::class, $data);

        self::assertSame('Dominik', $model->getName());
    }

    public function testDenormalizeByObject()
    {
        $denormalizer = new Denormalizer([
            new DenormalizationModelMapping(),
        ]);

        $data = ['name' => 'Dominik'];

        $model = $denormalizer->denormalize(new Model(), $data);

        self::assertSame('Dominik', $model->getName());
    }

    public function testDenormalizeWithAdditionalFieldsExpectsException()
    {
        self::expectException(DenormalizerException::class);
        self::expectExceptionMessage('There is an additional field at path: unknownField');

        $denormalizer = new Denormalizer([
            new DenormalizationModelMapping(),
        ]);

        $data = ['name' => 'Dominik', 'unknownField' => 'value'];

        $denormalizer->denormalize(Model::class, $data);
    }

    public function testDenormalizeWithAllowedAdditionalFields()
    {
        $denormalizer = new Denormalizer([
            new DenormalizationModelMapping(),
        ]);

        $data = ['name' => 'Dominik', 'unknownField' => 'value'];

        $model = $denormalizer->denormalize(Model::class, $data, new DenormalizerContext(true));

        self::assertSame('Dominik', $model->getName());
    }
}
