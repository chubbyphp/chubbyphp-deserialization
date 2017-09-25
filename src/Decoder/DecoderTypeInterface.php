<?php

declare(strict_types=1);

namespace Chubbyphp\Deserialization\Decoder;

interface DecoderTypeInterface
{
    /**
     * @return string
     */
    public function getContentType(): string;

    /**
     * @param string $data
     *
     * @return array
     *
     * @throws DecoderException
     */
    public function decode(string $data): array;
}
