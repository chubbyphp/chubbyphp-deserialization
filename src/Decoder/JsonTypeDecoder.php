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
        try {
            return json_decode($data, true);
        } catch (\TypeError $e) {
            throw DeserializerRuntimeException::createNotParsable($this->getContentType());
        }
    }
}
