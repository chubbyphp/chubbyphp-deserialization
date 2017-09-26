<?php

declare(strict_types=1);

namespace Chubbyphp\Deserialization\Decoder;

use Symfony\Component\Yaml\Yaml;

final class YamlDecoderType implements DecoderTypeInterface
{
    /**
     * @return string
     */
    public function getContentType(): string
    {
        return 'application/x-yaml';
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
            return Yaml::parse($data);
        } catch (\TypeError $e) {
            throw DecoderException::createNotParsable($this->getContentType());
        }
    }
}
