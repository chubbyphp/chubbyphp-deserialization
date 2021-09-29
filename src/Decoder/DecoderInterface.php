<?php

declare(strict_types=1);

namespace Chubbyphp\Deserialization\Decoder;

use Chubbyphp\Deserialization\DeserializerLogicException;
use Chubbyphp\Deserialization\DeserializerRuntimeException;

interface DecoderInterface
{
    /**
     * @return array<int, string>
     */
    public function getContentTypes(): array;

    /**
     * @throws DeserializerLogicException
     * @throws DeserializerRuntimeException
     *
     * @return array<string, null|array|bool|float|int|string>
     */
    public function decode(string $data, string $contentType): array;
}
