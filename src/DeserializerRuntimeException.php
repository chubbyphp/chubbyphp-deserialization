<?php

declare(strict_types=1);

namespace Chubbyphp\Deserialization;

final class DeserializerRuntimeException extends \RuntimeException
{
    public static function createInvalidDataType(string $path, string $givenType, string $wishedType): self
    {
        return new self(
            \sprintf('There is an invalid data type "%s", needed "%s" at path: "%s"', $givenType, $wishedType, $path)
        );
    }

    public static function createNotParsable(string $contentType, ?string $error = null): self
    {
        $message = \sprintf('Data is not parsable with content-type: "%s"', $contentType);
        if (null !== $error) {
            $message .= \sprintf(', error: "%s"', $error);
        }

        return new self($message);
    }

    /**
     * @param array<int, string> $paths
     */
    public static function createNotAllowedAdditionalFields(array $paths): self
    {
        return new self(\sprintf('There are additional field(s) at paths: "%s"', implode('", "', $paths)));
    }

    /**
     * @param array<int, string> $supportedTypes
     */
    public static function createMissingObjectType(string $path, array $supportedTypes): self
    {
        return new self(\sprintf(
            'Missing object type, supported are "%s" at path: "%s"',
            implode('", "', $supportedTypes),
            $path
        ));
    }

    /**
     * @param array<int, string> $supportedTypes
     */
    public static function createInvalidObjectType(string $path, string $type, array $supportedTypes): self
    {
        return new self(\sprintf(
            'Unsupported object type "%s", supported are "%s" at path: "%s"',
            $type,
            implode('", "', $supportedTypes),
            $path
        ));
    }

    public static function createTypeIsNotAString(string $path, string $dataType): self
    {
        return new self(\sprintf('Type is not a string, "%s" given at path: "%s"', $dataType, $path));
    }
}
