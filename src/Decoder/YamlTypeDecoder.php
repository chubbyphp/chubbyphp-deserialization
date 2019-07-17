<?php

declare(strict_types=1);

namespace Chubbyphp\Deserialization\Decoder;

use Chubbyphp\Deserialization\DeserializerRuntimeException;
use Symfony\Component\Yaml\Exception\ParseException;
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
     * @throws DeserializerRuntimeException
     *
     * @return array
     */
    public function decode(string $data): array
    {
        try {
            $decoded = Yaml::parse($data);
        } catch (ParseException $e) {
            throw DeserializerRuntimeException::createNotParsable($this->getContentType());
        }

        if (!is_array($decoded)) {
            throw DeserializerRuntimeException::createNotParsable($this->getContentType());
        }

        return $decoded;
    }
}
