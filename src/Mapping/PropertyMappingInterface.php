<?php

declare(strict_types=1);

namespace Chubbyphp\Deserialize\Mapping;

use Chubbyphp\Deserialize\Deserialize\PropertyDeserializeInterface;

interface PropertyMappingInterface
{
    /**
     * @return string
     */
    public function getName(): string;

    /**
     * @return PropertyDeserializeInterface
     */
    public function getPropertyDeserializer(): PropertyDeserializeInterface;
}
