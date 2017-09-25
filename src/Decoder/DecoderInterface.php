<?php

declare(strict_types=1);

namespace Chubbyphp\Deserialization\Decoder;

interface DecoderInterface
{
    /**
     * @return string[]
     */
    public function getContentTypes(): array;

    /**
     * @param string $data
     * @param string $contentType
     *
     * @return array
     *
     * @throws DecoderException
     */
    public function decode(string $data, string $contentType): array;
}
