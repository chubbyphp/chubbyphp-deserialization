<?php

declare(strict_types=1);

namespace Chubbyphp\Deserialization;

final class DeserializerLogicException extends \LogicException
{
    /**
     * @param string $contentType
     *
     * @return self
     */
    public static function createMissingContentType(string $contentType): self
    {
        return new self(sprintf('There is no decoder for content-type: %s', $contentType));
    }

    /**
     * @param string $class
     *
     * @return self
     */
    public static function createMissingMapping(string $class): self
    {
        return new self(sprintf('There is no mapping for class: %s', $class));
    }

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
