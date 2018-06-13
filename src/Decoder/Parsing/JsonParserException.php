<?php

declare(strict_types=1);

namespace Chubbyphp\Deserialization\Decoder\Parsing;

final class JsonParserException extends \RuntimeException
{
    /**
     * @param string $error
     * @return JsonParserException
     */
    public static function createFromError(string $error): self
    {
        return new self($error);
    }
}
