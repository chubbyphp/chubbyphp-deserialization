<?php

declare(strict_types=1);

namespace Chubbyphp\Deserialization;

final class DeserializerRuntimeException extends \RuntimeException
{
    /**
     * @param string $path
     *
     * @return self
     */
    public static function createInvalidType(string $path, string $type): self
    {
        return new self(sprintf('There is an invalid type "%s" at path: %s', $type, $path));
    }

    /**
     * @param string $contentType
     *
     * @return self
     */
    public static function createNotParsable(string $contentType): self
    {
        return new self(sprintf('Data is not parsable with content-type: %s', $contentType));
    }

    /**
     * @param array $paths
     *
     * @return self
     */
    public static function createNotAllowedAddtionalFields(array $paths): self
    {
        return new self(sprintf('There are additional field(s) at paths: %s', implode(', ', $paths)));
    }
}
