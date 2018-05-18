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
        return new self(sprintf('There is no decoder for content-type: "%s"', $contentType));
    }

    /**
     * @param string $path
     *
     * @return self
     */
    public static function createMissingDenormalizer(string $path): self
    {
        return new self(sprintf('There is no denormalizer at path: "%s"', $path));
    }

    /**
     * @param string $class
     *
     * @return self
     */
    public static function createMissingMapping(string $class): self
    {
        return new self(sprintf('There is no mapping for class: "%s"', $class));
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
            sprintf('There are no accessible method(s) "%s", within class: "%s"', implode('", "', $methods), $class)
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
        return new self(sprintf('There is no property "%s" within class: "%s"', $property, $class));
    }

    /**
     * @param string $path
     * @param string $dataType
     *
     * @return self
     */
    public static function createFactoryDoesNotReturnObject(string $path, string $dataType): self
    {
        return new self(sprintf('Factory does not return object, "%s" given at path: "%s"', $dataType, $path));
    }

    /**
     * @param string $convertType
     *
     * @return self
     */
    public static function createConvertTypeDoesNotExists(string $convertType): self
    {
        return new self(sprintf('Convert type "%s" is not supported', $convertType));
    }
}
