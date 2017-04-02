<?php

declare(strict_types=1);

namespace Chubbyphp\Tests\Deserialize\Resources\Mapping\Bidirectional;

use Chubbyphp\Deserialize\Callback\Simple\BidirectionalOneToManyCallback;
use Chubbyphp\Deserialize\Mapping\ObjectMappingInterface;
use Chubbyphp\Deserialize\Mapping\PropertyMapping;
use Chubbyphp\Deserialize\Mapping\PropertyMappingInterface;
use Chubbyphp\Tests\Deserialize\Resources\Model\Bidirectional\Many;
use Chubbyphp\Tests\Deserialize\Resources\Model\Bidirectional\One;

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
     * @return PropertyMappingInterface[]
     */
    public function getPropertyMappings(): array
    {
        return [
            new PropertyMapping('name'),
            new PropertyMapping('manies', new BidirectionalOneToManyCallback(Many::class, 'one')),
        ];
    }
}
