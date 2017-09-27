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
     * @param array $paths
     *
     * @return self
     */
    public static function createNotAllowedAddtionalFields(array $paths): self
    {
        return new self(sprintf('There are additional field(s) at paths: %s', implode(', ', $paths)));
    }
}
