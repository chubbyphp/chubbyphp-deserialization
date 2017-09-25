<?php

declare(strict_types=1);

namespace Chubbyphp\Deserialization\Denormalizer;

final class DenormalizerException extends \RuntimeException
{
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
     * @param string $path
     *
     * @return self
     */
    public static function createNotAllowedAddtionalField(string $path): self
    {
        return new self(sprintf('There is an additional field at path: %s', $path));
    }
}
