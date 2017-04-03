<?php

declare(strict_types=1);

namespace Chubbyphp\Tests\Deserialize\Resources\Mapping;

use Chubbyphp\Deserialize\Callback\OneToManyCallback;
use Chubbyphp\Deserialize\Mapping\ObjectMappingInterface;
use Chubbyphp\Deserialize\Mapping\PropertyMapping;
use Chubbyphp\Deserialize\Mapping\PropertyMappingInterface;
use Chubbyphp\Tests\Deserialize\Resources\Model\Many;
use Chubbyphp\Tests\Deserialize\Resources\Model\One;

final class OneMapping implements ObjectMappingInterface
{
    /**
     * @return string
     */
    public function getClass(): string
    {
        return One::class;
    }

    /**
     * @return string
     */
    public function getConstructMethod(): string
    {
        return 'create';
    }

    /**
     * @return PropertyMappingInterface[]
     */
    public function getPropertyMappings(): array
    {
        return [
            new PropertyMapping('name'),
            new PropertyMapping('manies', new OneToManyCallback(Many::class)),
        ];
    }
}
