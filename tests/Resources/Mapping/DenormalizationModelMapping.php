<?php

declare(strict_types=1);

namespace Chubbyphp\Tests\Deserialization\Resources\Mapping;

use Chubbyphp\Deserialization\Mapping\DenormalizingFieldMapping;
use Chubbyphp\Deserialization\Mapping\DenormalizingFieldMappingInterface;
use Chubbyphp\Deserialization\Mapping\DenormalizingObjectMappingInterface;
use Chubbyphp\Tests\Deserialization\Resources\Model\Model;

final class DenormalizationModelMapping implements DenormalizingObjectMappingInterface
{
    /**
     * @return string
     */
    public function getClass(): string
    {
        return Model::class;
    }

    /**
     * @return callable
     */
    public function getFactory(): callable
    {
        return function () {
            return new Model();
        };
    }

    /**
     * @return DenormalizingFieldMappingInterface[]
     */
    public function getDenormalizingFieldMappings(): array
    {
        return [
            new DenormalizingFieldMapping('name'),
        ];
    }
}
