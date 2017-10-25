<?php

declare(strict_types=1);

namespace Chubbyphp\Deserialization\Decoder;

use Chubbyphp\Deserialization\DeserializerRuntimeException;

final class JsonTypeDecoder implements TypeDecoderInterface
{
    /**
     * @return string
     */
    public function getContentType(): string
    {
        return 'application/json';
    }

    /**
     * @param string $data
     *
     * @return array
     *
     * @throws DeserializerRuntimeException
     */
    public function decode(string $data): array
    {
        if (false === is_array($json = json_decode($data, true)) || JSON_ERROR_NONE !== json_last_error()) {
            throw DeserializerRuntimeException::createNotParsable($this->getContentType());
        }

        return $json;
    }
}
