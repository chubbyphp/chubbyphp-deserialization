<?php

declare(strict_types=1);

namespace Chubbyphp\Deserialize;

final class MissingMappingException extends \InvalidArgumentException
{
    /**
     * @param string $class
     * @param string $property
     * @return MissingMappingException
     */
    public static function createByClassAndProperty(string $class, string $property): self
    {
        return new self(sprintf('Missing property mapping %s on class %s', $property, $class));
    }
}
