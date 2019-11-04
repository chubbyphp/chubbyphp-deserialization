<?php

declare(strict_types=1);

namespace Chubbyphp\Deserialization\Decoder;

use Chubbyphp\Deserialization\DeserializerLogicException;
use Chubbyphp\Deserialization\DeserializerRuntimeException;

final class Decoder implements DecoderInterface
{
    /**
     * @var TypeDecoderInterface[]
     */
    private $decoderTypes;

    /**
     * @param TypeDecoderInterface[] $decoderTypes
     */
    public function __construct(array $decoderTypes)
    {
        $this->decoderTypes = [];
        foreach ($decoderTypes as $decoderType) {
            $this->addTypeDecoder($decoderType);
        }
    }

    public function getContentTypes(): array
    {
        return array_keys($this->decoderTypes);
    }

    /**
     * @throws DeserializerLogicException
     * @throws DeserializerRuntimeException
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
