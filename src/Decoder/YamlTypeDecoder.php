<?php

declare(strict_types=1);

namespace Chubbyphp\Deserialization\Decoder;

use Chubbyphp\Deserialization\DeserializerRuntimeException;
use Symfony\Component\Yaml\Exception\ParseException;
use Symfony\Component\Yaml\Yaml;

final class YamlTypeDecoder implements TypeDecoderInterface
{
    public function getContentType(): string
    {
        return 'application/x-yaml';
    }

    /**
     * @throws DeserializerRuntimeException
     *
     * @return array<string, array|string|float|int|bool|null>
     */
    public function decode(string $data): array
    {
        try {
            $decoded = Yaml::parse($data);
        } catch (ParseException $exception) {
            throw DeserializerRuntimeException::createNotParsable($this->getContentType());
        }

        if (!is_array($decoded)) {
            throw DeserializerRuntimeException::createNotParsable($this->getContentType(), 'Not an object');
        }

        return $decoded;
    }
}
