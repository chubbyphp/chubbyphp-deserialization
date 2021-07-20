<?php

declare(strict_types=1);

namespace Chubbyphp\Deserialization;

final class DeserializerLogicException extends \LogicException
{
    public static function createMissingContentType(string $contentType): self
    {
        return new self(sprintf('There is no decoder for content-type: "%s"', $contentType));
    }

    public static function createMissingDenormalizer(string $path): self
    {
        return new self(sprintf('There is no denormalizer at path: "%s"', $path));
    }

    public static function createMissingMapping(string $class): self
    {
        return new self(sprintf('There is no mapping for class: "%s"', $class));
    }

    /**
     * @param array<int, string> $methods
     */
    public static function createMissingMethod(string $class, array $methods): self
    {
        return new self(
            sprintf('There are no accessible method(s) "%s", within class: "%s"', implode('", "', $methods), $class)
        );
    }

    public static function createMissingProperty(string $class, string $property): self
    {
        return new self(sprintf('There is no property "%s" within class: "%s"', $property, $class));
    }

    public static function createFactoryDoesNotReturnObject(string $path, string $dataType): self
    {
        return new self(sprintf('Factory does not return object, "%s" given at path: "%s"', $dataType, $path));
    }

    public static function createConvertTypeDoesNotExists(string $convertType): self
    {
        return new self(sprintf('Convert type "%s" is not supported', $convertType));
    }

    public static function createMissingParentAccessor(string $path): self
    {
        return new self(sprintf('There is no parent accessor at path: "%s"', $path));
    }
}
