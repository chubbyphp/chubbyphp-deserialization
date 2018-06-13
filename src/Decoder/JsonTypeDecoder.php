<?php

declare(strict_types=1);

namespace Chubbyphp\Deserialization\Decoder;

use Chubbyphp\Deserialization\Decoder\Parsing\JsonParserException;
use Chubbyphp\Deserialization\Decoder\Parsing\JsonParserInterface;
use Chubbyphp\Deserialization\Decoder\Parsing\NativeJsonParser;
use Chubbyphp\Deserialization\DeserializerRuntimeException;

final class JsonTypeDecoder implements TypeDecoderInterface
{

    /**
     * @var JsonParserInterface
     */
    private $jsonParser;

    /**
     * JsonTypeDecoder constructor.
     * @param JsonParserInterface $jsonParser
     */
    public function __construct(JsonParserInterface $jsonParser = null)
    {
        $this->jsonParser = $jsonParser ?? new NativeJsonParser();
    }

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
            $decoded = $this->jsonParser->parse($data);
        } catch (JsonParserException $exception) {
            throw DeserializerRuntimeException::createNotParsable($this->getContentType(), $exception->getMessage());
        }

        if (!is_array($decoded)) {
            throw DeserializerRuntimeException::createNotParsable($this->getContentType(), 'Not an object');
        }

        return $decoded;
    }
}
