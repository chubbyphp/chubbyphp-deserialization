<?php

declare(strict_types=1);

namespace Chubbyphp\Deserialization\Registry;

use Chubbyphp\Deserialization\Mapping\ObjectMappingInterface;

interface ObjectMappingRegistryInterface
{
    /**
     * @param string $class
     * @return ObjectMappingInterface
     * @throws \InvalidArgumentException
     */
    public function getObjectMappingForClass(string $class): ObjectMappingInterface;
}
