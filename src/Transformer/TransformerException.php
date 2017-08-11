<?php

declare(strict_types=1);

namespace Chubbyphp\Deserialization\Transformer;

final class TransformerException extends \InvalidArgumentException
{
    /**
     * @param string $message
     *
     * @return self
     */
    public static function create(string $message): self
    {
        return new self(sprintf('Transform error: %s', $message));
    }
}
