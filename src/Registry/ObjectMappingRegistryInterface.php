<?php

declare(strict_types=1);

namespace Chubbyphp\Deserialize\Registry;

use Chubbyphp\Deserialize\Mapping\ObjectMappingInterface;

interface ObjectMappingRegistryInterface
{
    /**
     * @param string $class
     *
     * @return ObjectMappingInterface
     *
     * @throws \InvalidArgumentException
     */
    public function getObjectMappingForClass(string $class): ObjectMappingInterface;
}
