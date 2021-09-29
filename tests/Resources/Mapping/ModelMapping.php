<?php

declare(strict_types=1);

namespace Chubbyphp\Tests\Deserialization\Resources\Mapping;

use Chubbyphp\Deserialization\DeserializerRuntimeException;
use Chubbyphp\Deserialization\Mapping\DenormalizationFieldMappingBuilder;
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
     * @throws DeserializerRuntimeException
     *
     * @return array<int, DenormalizationFieldMappingInterface>
     */
    public function getDenormalizationFieldMappings(string $path, ?string $type = null): array
    {
        return [
            DenormalizationFieldMappingBuilder::create('name')->getMapping(),
            DenormalizationFieldMappingBuilder::createEmbedOne('one', OneModel::class)->getMapping(),
            DenormalizationFieldMappingBuilder::createEmbedMany('manies', AbstractManyModel::class)->getMapping(),
        ];
    }
}
