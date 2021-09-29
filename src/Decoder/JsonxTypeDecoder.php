<?php

declare(strict_types=1);

namespace Chubbyphp\Deserialization\Decoder;

use Chubbyphp\Deserialization\DeserializerRuntimeException;

/**
 * @see https://www.ibm.com/support/knowledgecenter/SS9H2Y_7.6.0/com.ibm.dp.doc/json_jsonx.html
 */
final class JsonxTypeDecoder implements TypeDecoderInterface
{
    private const DATATYPE_OBJECT = 'object';
    private const DATATYPE_ARRAY = 'array';
    private const DATATYPE_BOOLEAN = 'boolean';
    private const DATATYPE_STRING = 'string';
    private const DATATYPE_NUMBER = 'number';
    private const DATATYPE_NULL = 'null';

    public function getContentType(): string
    {
        return 'application/jsonx+xml';
    }

    /**
     * @throws DeserializerRuntimeException
     *
     * @return array<string, null|array|bool|float|int|string>
     */
    public function decode(string $data): array
    {
        $document = new \DOMDocument();

        if (!@$document->loadXML($data)) {
            throw DeserializerRuntimeException::createNotParsable($this->getContentType());
        }

        return $this->decodeNode($document->documentElement);
    }

    /**
     * @return null|array|bool|float|int|string
     */
    private function decodeNode(\DOMNode $node)
    {
        $nodeName = $node->nodeName;

        $nodeType = mb_substr($nodeName, 5);

        if (self::DATATYPE_OBJECT === $nodeType) {
            return $this->decodeObjectNode($node);
        }

        if (self::DATATYPE_ARRAY === $nodeType) {
            return $this->decodeArrayNode($node);
        }

        if (self::DATATYPE_BOOLEAN === $nodeType) {
            return $this->decodeBooleanNode($node);
        }

        if (self::DATATYPE_STRING === $nodeType) {
            return $this->decodeStringNode($node);
        }

        if (self::DATATYPE_NUMBER === $nodeType) {
            return $this->decodeNumberNode($node);
        }

        if (self::DATATYPE_NULL === $nodeType) {
            return;
        }

        throw DeserializerRuntimeException::createNotParsable($this->getContentType());
    }

    /**
     * @return array<string, null|array|bool|float|int|string>
     */
    private function decodeObjectNode(\DOMNode $node): array
    {
        $data = [];
        foreach ($node->childNodes as $childNode) {
            if ($childNode instanceof \DOMText) {
                continue;
            }

            $data[$childNode->getAttribute('name')] = $this->decodeNode($childNode);
        }

        return $data;
    }

    /**
     * @return array<int, null|array|bool|float|int|string>
     */
    private function decodeArrayNode(\DOMNode $node): array
    {
        $data = [];
        foreach ($node->childNodes as $childNode) {
            if ($childNode instanceof \DOMText) {
                continue;
            }

            $data[] = $this->decodeNode($childNode);
        }

        return $data;
    }

    private function decodeBooleanNode(\DOMNode $node): bool
    {
        return 'true' === $node->nodeValue;
    }

    private function decodeStringNode(\DOMNode $node): string
    {
        return html_entity_decode($node->nodeValue, ENT_COMPAT | ENT_XML1, 'UTF-8');
    }

    /**
     * @return float|int
     */
    private function decodeNumberNode(\DOMNode $node)
    {
        $value = $node->nodeValue;

        if ($value === (string) (int) $value) {
            return (int) $value;
        }

        return (float) $value;
    }
}
