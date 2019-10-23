<?php

declare(strict_types=1);

namespace Chubbyphp\Deserialization\Decoder;

use Chubbyphp\Deserialization\DeserializerRuntimeException;

/**
 * @deprecated use JsonxTypeDecoder instead
 */
final class XmlTypeDecoder implements TypeDecoderInterface
{
    /**
     * @return string
     */
    public function getContentType(): string
    {
        return 'application/xml';
    }

    /**
     * @param string $data
     *
     * @throws DeserializerRuntimeException
     *
     * @return array
     */
    public function decode(string $data): array
    {
        $document = new \DOMDocument();

        if (!@$document->loadXML($data)) {
            throw DeserializerRuntimeException::createNotParsable($this->getContentType());
        }

        return $this->transformType($document->getElementsByTagName('object')->item(0));
    }

    /**
     * @param \DOMElement $node
     *
     * @return array
     */
    private function transformType(\DOMElement $node): array
    {
        $data = [];

        $childNodes = [];
        foreach ($node->childNodes as $childNode) {
            if ($childNode instanceof \DOMElement) {
                $childNodes[] = $childNode;
            }
        }

        foreach ($childNodes as $childNode) {
            if (0 === $childNode->childNodes->length) {
                $data[$this->getKey($childNode)] = null;

                continue;
            }

            if (1 === $childNode->childNodes->length) {
                $data[$this->getKey($childNode)] = $this->getValue($childNode);

                continue;
            }

            if ('object' === $childNode->nodeName
                && $childNode->hasAttribute('type')
                && !$childNode->hasAttribute('key')
            ) {
                return $this->transformType($childNode);
            }

            $data[$this->getKey($childNode)] = $this->transformType($childNode);
        }

        if ('object' === $node->nodeName && $node->hasAttribute('type')) {
            $data['_type'] = $node->getAttribute('type');
        }

        return $data;
    }

    /**
     * @param \DOMElement $node
     *
     * @return string|int
     */
    private function getKey(\DOMElement $node)
    {
        if ($node->hasAttribute('key')) {
            return (int) $node->getAttribute('key');
        }

        $name = $node->nodeName;
        if (0 === strpos($name, 'meta-')) {
            $name = '_'.substr($name, 5);
        }

        return $name;
    }

    /**
     * @param \DOMElement $node
     *
     * @return bool|string
     */
    private function getValue(\DOMElement $node)
    {
        $value = $node->nodeValue;

        if ('boolean' === $type = $node->getAttribute('type')) {
            return 'true' === $value;
        }

        settype($value, $type);

        return $value;
    }
}
