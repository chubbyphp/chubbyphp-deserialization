<?php

declare(strict_types=1);

namespace Chubbyphp\Deserialization;

use Chubbyphp\Deserialization\Decoder\DecoderInterface;
use Chubbyphp\Deserialization\Denormalizer\DenormalizerContextInterface;
use Chubbyphp\Deserialization\Denormalizer\DenormalizerInterface;

final class Deserializer implements DeserializerInterface
{
    private DecoderInterface $decoder;

    private DenormalizerInterface $denormalizer;

    public function __construct(DecoderInterface $decoder, DenormalizerInterface $denormalizer)
    {
        $this->decoder = $decoder;
        $this->denormalizer = $denormalizer;
    }

    /**
     * @param object|string $object
     */
    public function deserialize(
        $object,
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
     * @throws DeserializerLogicException
     * @throws DeserializerRuntimeException
     *
     * @return array<string, null|array|bool|float|int|string>
     */
    public function decode(string $data, string $contentType): array
    {
        return $this->decoder->decode($data, $contentType);
    }

    /**
     * @param object|string                                   $object
     * @param array<string, null|array|bool|float|int|string> $data
     *
     * @throws DeserializerLogicException
     * @throws DeserializerRuntimeException
     */
    public function denormalize(
        $object,
        array $data,
        ?DenormalizerContextInterface $context = null,
        string $path = ''
    ): object {
        return $this->denormalizer->denormalize($object, $data, $context, $path);
    }
}
