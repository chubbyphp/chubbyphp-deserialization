<?php

declare(strict_types=1);

namespace Chubbyphp\Deserialization\Decoder;

final class XmlTypeDecoder implements TypeDecoderInterface
{
    private JsonxTypeDecoder $jsonxTypeDecoder;

    public function __construct()
    {
        $this->jsonxTypeDecoder = new JsonxTypeDecoder();
    }

    public function getContentType(): string
    {
        return 'application/xml';
    }

    /**
     * @return array<string, array|string|float|int|bool|null>
     */
    public function decode(string $data): array
    {
        return $this->jsonxTypeDecoder->decode($data);
    }
}
