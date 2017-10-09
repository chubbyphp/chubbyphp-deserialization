<?php

declare(strict_types=1);

namespace Chubbyphp\Deserialization\Decoder;

use Chubbyphp\Deserialization\DeserializerLogicException;

final class Decoder implements DecoderInterface
{
    /**
     * @var DecoderTypeInterface[]
     */
    private $decoderTypes;

    /**
     * @param DecoderTypeInterface[] $decoderTypes
     */
    public function __construct(array $decoderTypes)
    {
        $this->decoderTypes = [];
        foreach ($decoderTypes as $decoderType) {
            $this->addDecoderType($decoderType);
        }
    }

    /**
     * @param DecoderTypeInterface $decoderType
     */
    private function addDecoderType(DecoderTypeInterface $decoderType)
    {
        $this->decoderTypes[$decoderType->getContentType()] = $decoderType;
    }

    /**
     * @return array
     */
    public function getContentTypes(): array
    {
        return array_keys($this->decoderTypes);
    }

    /**
     * @param string $data
     * @param string $contentType
     *
     * @return array
     *
     * @throws DeserializerLogicException
     */
    public function decode(string $data, string $contentType): array
    {
        if (isset($this->decoderTypes[$contentType])) {
            return $this->decoderTypes[$contentType]->decode($data);
        }

        throw DeserializerLogicException::createMissingContentType($contentType);
    }
}
