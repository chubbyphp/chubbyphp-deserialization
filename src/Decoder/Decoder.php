<?php

declare(strict_types=1);

namespace Chubbyphp\Deserialization\Decoder;

use Chubbyphp\Deserialization\DeserializerLogicException;
use Chubbyphp\Deserialization\DeserializerRuntimeException;

final class Decoder implements DecoderInterface
{
    /**
     * @var array<string, TypeDecoderInterface>
     */
    private array $decoderTypes;

    /**
     * @param array<int, TypeDecoderInterface> $decoderTypes
     */
    public function __construct(array $decoderTypes)
    {
        $this->decoderTypes = [];
        foreach ($decoderTypes as $decoderType) {
            $this->addTypeDecoder($decoderType);
        }
    }

    /**
     * @return array<int, string>
     */
    public function getContentTypes(): array
    {
        return array_keys($this->decoderTypes);
    }

    /**
     * @throws DeserializerLogicException
     * @throws DeserializerRuntimeException
     *
     * @return array<string, array|string|float|int|bool|null>
     */
    public function decode(string $data, string $contentType): array
    {
        if (isset($this->decoderTypes[$contentType])) {
            return $this->decoderTypes[$contentType]->decode($data);
        }

        throw DeserializerLogicException::createMissingContentType($contentType);
    }

    private function addTypeDecoder(TypeDecoderInterface $decoderType): void
    {
        $this->decoderTypes[$decoderType->getContentType()] = $decoderType;
    }
}
