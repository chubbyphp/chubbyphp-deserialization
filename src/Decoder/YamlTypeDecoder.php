<?php

declare(strict_types=1);

namespace Chubbyphp\Deserialization\Decoder;

use Chubbyphp\Deserialization\DeserializerRuntimeException;
use Symfony\Component\Yaml\Yaml;

final class YamlTypeDecoder implements TypeDecoderInterface
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
     * @throws DeserializerRuntimeException
     */
    public function decode(string $data): array
    {
        try {
            return Yaml::parse($data);
        } catch (\TypeError $e) {
            throw DeserializerRuntimeException::createNotParsable($this->getContentType());
        }
    }
}
