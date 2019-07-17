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
     * @throws DeserializerRuntimeException
     *
     * @return array
     */
    public function decode(string $data): array;
}
