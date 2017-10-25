<?php

declare(strict_types=1);

namespace Chubbyphp\Deserialization\Decoder;

use Chubbyphp\Deserialization\DeserializerLogicException;
use Chubbyphp\Deserialization\DeserializerRuntimeException;

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
     * @throws DeserializerRuntimeException
     */
    public function decode(string $data, string $contentType): array;
}
