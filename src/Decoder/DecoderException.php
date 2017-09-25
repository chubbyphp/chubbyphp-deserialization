<?php

declare(strict_types=1);

namespace Chubbyphp\Deserialization\Decoder;

final class DecoderException extends \RuntimeException
{
    /**
     * @param string $contentType
     *
     * @return self
     */
    public static function createMissing(string $contentType): self
    {
        return new self(sprintf('There is no decoder for content-type: %s', $contentType));
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
}
