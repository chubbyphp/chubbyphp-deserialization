<?php

declare(strict_types=1);

namespace Chubbyphp\Deserialization\Decoder\Parsing;

final class NativeJsonParser implements JsonParserInterface
{
    /**
     * @param string $json
     * @return array|string|float|int|bool|null
     * @throws JsonParserException
     */
    public function parse(string $json)
    {
        $data = json_decode($json, true);

        if (JSON_ERROR_NONE !== json_last_error()) {
            throw JsonParserException::createFromError(json_last_error_msg());
        }

        return $data;
    }
}
