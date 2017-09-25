<?php

declare(strict_types=1);

namespace Chubbyphp\Deserialization\Decoder;

final class Decoder implements DecoderInterface
{
    /**
     * @var DecoderTypeInterface[]
     */
    private $decoders;

    /**
     * @param DecoderTypeInterface[] $decoders
     */
    public function __construct(array $decoders)
    {
        $this->decoders = [];
        foreach ($decoders as $decoder) {
            $this->addDecoder($decoder);
        }
    }

    /**
     * @param DecoderTypeInterface $decoder
     */
    private function addDecoder(DecoderTypeInterface $decoder)
    {
        $this->decoders[$decoder->getContentType()] = $decoder;
    }

    /**
     * @return array
     */
    public function getContentTypes(): array
    {
        return array_keys($this->decoders);
    }

    /**
     * @param string $data
     * @param string $contentType
     *
     * @return array
     *
     * @throws DecoderException
     */
    public function decode(string $data, string $contentType): array
    {
        if (isset($this->decoders[$contentType])) {
            return $this->decoders[$contentType]->decode($data);
        }

        throw DecoderException::createMissing($contentType);
    }
}
