<?php

declare(strict_types=1);

namespace Chubbyphp\Tests\Deserialization\Resources\Mapping;

use Chubbyphp\Deserialization\Accessor\PropertyAccessor;
use Chubbyphp\Deserialization\Denormalizer\Relation\EmbedManyFieldDenormalizer;
use Chubbyphp\Deserialization\Denormalizer\Relation\EmbedOneFieldDenormalizer;
use Chubbyphp\Deserialization\DeserializerRuntimeException;
use Chubbyphp\Deserialization\Mapping\DenormalizationFieldMappingBuilder;
use Chubbyphp\Deserialization\Mapping\DenormalizationFieldMappingInterface;
use Chubbyphp\Deserialization\Mapping\DenormalizationObjectMappingInterface;
use Chubbyphp\Tests\Deserialization\Resources\Model\AbstractManyModel;
use Chubbyphp\Tests\Deserialization\Resources\Model\Model;
use Chubbyphp\Tests\Deserialization\Resources\Model\OneModel;

final class ModelMapping implements DenormalizationObjectMappingInterface
{
    /**
     * @return string
     */
    public function getClass(): string
    {
        return Model::class;
    }

    /**
     * @param string      $path
     * @param string|null $type
     *
     * @return callable
     *
     * @throws DeserializerRuntimeException
     */
    public function getDenormalizationFactory(string $path, string $type = null): callable
    {
        return function () {
            return new Model();
        };
    }

    /**
     * @param string      $path
     * @param string|null $type
     *
     * @return DenormalizationFieldMappingInterface[]
     *
     * @throws DeserializerRuntimeException
     */
    public function getDenormalizationFieldMappings(string $path, string $type = null): array
    {
        return [
            DenormalizationFieldMappingBuilder::create('name')->getMapping(),
            DenormalizationFieldMappingBuilder::create('one')->setFieldDenormalizer(
                new EmbedOneFieldDenormalizer(OneModel::class, new PropertyAccessor('one'))
            )->getMapping(),
            DenormalizationFieldMappingBuilder::create('manies')->setFieldDenormalizer(
                new EmbedManyFieldDenormalizer(AbstractManyModel::class, new PropertyAccessor('manies'))
            )->getMapping(),
        ];
    }
}
