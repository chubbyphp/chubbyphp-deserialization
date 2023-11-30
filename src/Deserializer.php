<?php

declare(strict_types=1);

namespace Chubbyphp\Deserialization;

use Chubbyphp\DecodeEncode\Decoder\DecoderInterface;
use Chubbyphp\Deserialization\Denormalizer\DenormalizerContextInterface;
use Chubbyphp\Deserialization\Denormalizer\DenormalizerInterface;

final class Deserializer implements DeserializerInterface
{
    public function __construct(private DecoderInterface $decoder, private DenormalizerInterface $denormalizer) {}

    public function deserialize(
        object|string $object,
        string $data,
        string $contentType,
        ?DenormalizerContextInterface $context = null,
        string $path = ''
    ): object {
        return $this->denormalizer->denormalize($object, $this->decoder->decode($data, $contentType), $context, $path);
    }

    /**
     * @return array<int, string>
     */
    public function getContentTypes(): array
    {
        return $this->decoder->getContentTypes();
    }

    /**
     * @return array<string, null|array|bool|float|int|string>
     *
     * @throws DeserializerLogicException
     * @throws DeserializerRuntimeException
     */
    public function decode(string $data, string $contentType): array
    {
        return $this->decoder->decode($data, $contentType);
    }

    /**
     * @param array<string, null|array|bool|float|int|string> $data
     *
     * @throws DeserializerLogicException
     * @throws DeserializerRuntimeException
     */
    public function denormalize(
        object|string $object,
        array $data,
        ?DenormalizerContextInterface $context = null,
        string $path = ''
    ): object {
        return $this->denormalizer->denormalize($object, $data, $context, $path);
    }
}
