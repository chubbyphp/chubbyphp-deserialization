<?php

declare(strict_types=1);

namespace Chubbyphp\Deserialization\Mapping;

use Chubbyphp\Deserialization\Deserializer\PropertyDeserializerInterface;

interface PropertyMappingInterface
{
    /**
     * @return string
     */
    public function getName(): string;

    /**
     * @return PropertyDeserializerInterface
     */
    public function getPropertyDeserializer(): PropertyDeserializerInterface;
}
