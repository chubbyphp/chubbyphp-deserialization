<?php

declare(strict_types=1);

namespace Chubbyphp\Deserialization\Decoder;

use Chubbyphp\Deserialization\DeserializerRuntimeException;

final class JsonTypeDecoder implements TypeDecoderInterface
{
    public function getContentType(): string
    {
        return 'application/json';
    }

    /**
     * @throws DeserializerRuntimeException
     *
     * @return array<string, array|string|float|int|bool|null>
     */
    public function decode(string $data): array
    {
        $decoded = json_decode($data, true);

        if (JSON_ERROR_NONE !== json_last_error()) {
            throw DeserializerRuntimeException::createNotParsable($this->getContentType(), json_last_error_msg());
        }

        if (!is_array($decoded)) {
            throw DeserializerRuntimeException::createNotParsable($this->getContentType(), 'Not an object');
        }

        return $decoded;
    }
}
