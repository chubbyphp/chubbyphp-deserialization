<?php

declare(strict_types=1);

namespace Chubbyphp\Deserialization\Decoder;

final class JsonDecoderType implements DecoderTypeInterface
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
     * @throws DecoderException
     */
    public function decode(string $data): array
    {
        try {
            return json_decode($data, true);
        } catch (\TypeError $e) {
            throw DecoderException::createNotParsable($this->getContentType());
        }
    }
}
