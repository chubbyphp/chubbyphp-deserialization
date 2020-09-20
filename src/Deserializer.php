<?php

declare(strict_types=1);

namespace Chubbyphp\Deserialization;

use Chubbyphp\Deserialization\Decoder\DecoderInterface;
use Chubbyphp\Deserialization\Denormalizer\DenormalizerContextInterface;
use Chubbyphp\Deserialization\Denormalizer\DenormalizerInterface;

final class Deserializer implements DeserializerInterface
{
    /**
     * @var DecoderInterface
     */
    private $decoder;

    /**
     * @var DenormalizerInterface
     */
    private $denormalizer;

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
     * @return array<string, array|string|float|int|bool|null>
     */
    public function decode(string $data, string $contentType): array
    {
        return $this->decoder->decode($data, $contentType);
    }

    /**
     * @param object|string                                   $object
     * @param array<string, array|string|float|int|bool|null> $data
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
