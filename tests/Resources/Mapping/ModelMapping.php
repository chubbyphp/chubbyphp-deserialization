<?php

declare(strict_types=1);

namespace Chubbyphp\Tests\Deserialization\Resources\Mapping;

use Chubbyphp\Deserialization\DeserializerRuntimeException;
use Chubbyphp\Deserialization\Mapping\DenormalizationFieldMappingFactory;
use Chubbyphp\Deserialization\Mapping\DenormalizationFieldMappingInterface;
use Chubbyphp\Deserialization\Mapping\DenormalizationObjectMappingInterface;
use Chubbyphp\Tests\Deserialization\Resources\Model\AbstractManyModel;
use Chubbyphp\Tests\Deserialization\Resources\Model\Model;
use Chubbyphp\Tests\Deserialization\Resources\Model\OneModel;

final class ModelMapping implements DenormalizationObjectMappingInterface
{
    public function getClass(): string
    {
        return Model::class;
    }

    /**
     * @throws DeserializerRuntimeException
     */
    public function getDenormalizationFactory(string $path, ?string $type = null): callable
    {
        return static fn () => new Model();
    }

    /**
     * @return array<int, DenormalizationFieldMappingInterface>
     *
     * @throws DeserializerRuntimeException
     */
    public function getDenormalizationFieldMappings(string $path, ?string $type = null): array
    {
        $denormalizationFieldMappingFactory = new DenormalizationFieldMappingFactory();

        return [
            $denormalizationFieldMappingFactory->create('name'),
            $denormalizationFieldMappingFactory->createEmbedOne('one', OneModel::class),
            $denormalizationFieldMappingFactory->createEmbedMany('manies', AbstractManyModel::class),
        ];
    }
}
