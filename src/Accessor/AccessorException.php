<?php

declare(strict_types=1);

namespace Chubbyphp\Deserialization\Accessor;

final class AccessorException extends \RuntimeException
{
    /**
     * @param string $class
     * @param array  $methods
     *
     * @return self
     */
    public static function createMissingMethod(string $class, array $methods): self
    {
        return new self(
            sprintf('Class %s does not contain an accessable method(s) %s', $class, implode(', ', $methods))
        );
    }

    /**
     * @param string $class
     * @param string $property
     *
     * @return self
     */
    public static function createMissingProperty(string $class, string $property): self
    {
        return new self(sprintf('Class %s does not contain property %s', $class, $property));
    }
}
