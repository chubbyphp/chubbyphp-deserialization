<?php

declare(strict_types=1);

namespace Chubbyphp\Deserialize\Mapping;

use Chubbyphp\Deserialize\Deserializer\PropertyDeserializerInterface;

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
