<?php

declare(strict_types=1);

namespace Chubbyphp\Deserialization\Decoder\Parsing;

use Seld\JsonLint\JsonParser;
use Seld\JsonLint\ParsingException;

final class SeldJsonLintJsonParser implements JsonParserInterface
{
    /**
     * @var JsonParser
     */
    private $parser;

    public function __construct()
    {
        $this->parser = new JsonParser();
    }

    /**
     * @param string $json
     * @return array|string|float|int|bool|null
     * @throws JsonParserException
     */
    public function parse(string $json)
    {
        try {
            return $this->parser->parse($json, JsonParser::PARSE_TO_ASSOC);
        } catch (ParsingException $e) {
            throw JsonParserException::createFromError($e->getMessage());
        }
    }
}
