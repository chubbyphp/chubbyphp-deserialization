<?php

declare(strict_types=1);

namespace Chubbyphp\Deserialization\Transformer;

final class XmlTransformer implements TransformerInterface
{
    /**
     * @param string $string
     *
     * @return array
     */
    public function transform(string $string): array
    {
        $document = new \DOMDocument();
        $document->loadXML($string);

        return $this->transformType($document->getElementsByTagName('meta-type')->item(0));
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

            if ('meta-type' === $childNode->nodeName && !$childNode->hasAttribute('key')) {
                return $this->transformType($childNode);
            }

            $data[$this->getKey($childNode)] = $this->transformType($childNode);
        }

        if ('meta-type' === $node->nodeName && $node->hasAttribute('value')) {
            $data['_type'] = $node->getAttribute('value');
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
