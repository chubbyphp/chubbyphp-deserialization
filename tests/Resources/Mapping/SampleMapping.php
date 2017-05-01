<?php

declare(strict_types=1);

namespace Chubbyphp\Tests\Deserialize\Resources\Mapping;

use Chubbyphp\Deserialize\Mapping\ObjectMappingInterface;
use Chubbyphp\Deserialize\Mapping\PropertyMapping;
use Chubbyphp\Deserialize\Mapping\PropertyMappingInterface;
use Chubbyphp\Tests\Deserialize\Resources\Model\Sample;

final class SampleMapping implements ObjectMappingInterface
{
    /**
     * @return string
     */
    public function getClass(): string
    {
        return Sample::class;
    }

    /**
     * @return callable
     */
    public function getFactory(): callable
    {
        return [Sample::class, 'create'];
    }

    /**
     * @return PropertyMappingInterface[]
     */
    public function getPropertyMappings(): array
    {
        return [
            new PropertyMapping('name'),
        ];
    }
}
