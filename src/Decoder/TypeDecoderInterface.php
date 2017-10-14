<?php

declare(strict_types=1);

namespace Chubbyphp\Deserialization\Decoder;

use Chubbyphp\Deserialization\DeserializerRuntimeException;

interface TypeDecoderInterface
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
     * @throws DeserializerRuntimeException
     */
    public function decode(string $data): array;
}
