<?php

declare(strict_types=1);

namespace Chubbyphp\Deserialization\Decoder\Parsing;

interface JsonParserInterface
{
    /**
     * @param string $json
     * @return array|string|float|int|bool|null
     * @throws JsonParserException
     */
    public function parse(string $json);
}
