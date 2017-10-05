<?php

declare(strict_types=1);

namespace Chubbyphp\Deserialization\Decoder;

use Chubbyphp\Deserialization\DeserializerLogicException;

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
     * @throws DeserializerLogicException
     */
    public function decode(string $data, string $contentType): array;
}
