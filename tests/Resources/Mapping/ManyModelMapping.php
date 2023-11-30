<?php

declare(strict_types=1);

namespace Chubbyphp\Tests\Deserialization\Resources\Mapping;

use Chubbyphp\Deserialization\DeserializerRuntimeException;
use Chubbyphp\Deserialization\Mapping\DenormalizationFieldMappingFactory;
use Chubbyphp\Deserialization\Mapping\DenormalizationFieldMappingInterface;
use Chubbyphp\Deserialization\Mapping\DenormalizationObjectMappingInterface;
use Chubbyphp\Tests\Deserialization\Resources\Model\ManyModel;

final class ManyModelMapping implements DenormalizationObjectMappingInterface
{
    public function getClass(): string
    {
        return ManyModel::class;
    }

    /**
     * @throws DeserializerRuntimeException
     */
    public function getDenormalizationFactory(string $path, ?string $type = null): callable
    {
        return static fn () => new ManyModel();
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
            $denormalizationFieldMappingFactory->create('value'),
        ];
    }
}
