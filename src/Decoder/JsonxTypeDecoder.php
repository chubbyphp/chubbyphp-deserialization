<?php

declare(strict_types=1);

namespace Chubbyphp\Deserialization\Decoder;

use Chubbyphp\Deserialization\DeserializerRuntimeException;

/**
 * @see https://www.ibm.com/support/knowledgecenter/SS9H2Y_7.6.0/com.ibm.dp.doc/json_jsonx.html
 */
final class JsonxTypeDecoder implements TypeDecoderInterface
{
    const DATATYPE_OBJECT = 'object';
    const DATATYPE_ARRAY = 'array';
    const DATATYPE_BOOLEAN = 'boolean';
    const DATATYPE_STRING = 'string';
    const DATATYPE_NUMBER = 'number';
    const DATATYPE_NULL = 'null';

    /**
     * @return string
     */
    public function getContentType(): string
    {
        return 'application/x-jsonx';
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
        $document = new \DOMDocument();

        if (!@$document->loadXML($data)) {
            throw DeserializerRuntimeException::createNotParsable($this->getContentType());
        }

        return $this->decodeNode($document->documentElement);
    }

    /**
     * @param \DOMNode $node
     *
     * @return array|bool|string|int|float|null
     */
    private function decodeNode(\DOMNode $node)
    {
        $nodeName = $node->nodeName;

        $nodeType = substr($nodeName, 5);

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
            return null;
        }

        throw DeserializerRuntimeException::createNotParsable($this->getContentType());
    }

    /**
     * @param \DOMNode $node
     *
     * @return array
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
     * @param \DOMNode $node
     *
     * @return array
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

    /**
     * @param \DOMNode $node
     *
     * @return bool
     */
    private function decodeBooleanNode(\DOMNode $node): bool
    {
        return 'true' === $node->nodeValue;
    }

    /**
     * @param \DOMNode $node
     *
     * @return string
     */
    private function decodeStringNode(\DOMNode $node): string
    {
        return html_entity_decode($node->nodeValue, ENT_COMPAT | ENT_XML1, 'UTF-8');
    }

    /**
     * @param \DOMNoDOMNodedeList $node
     *
     * @return int|float
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
