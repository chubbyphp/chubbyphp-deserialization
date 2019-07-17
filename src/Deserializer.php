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

    /**
     * @param DecoderInterface      $decoder
     * @param DenormalizerInterface $denormalizer
     */
    public function __construct(DecoderInterface $decoder, DenormalizerInterface $denormalizer)
    {
        $this->decoder = $decoder;
        $this->denormalizer = $denormalizer;
    }

    /**
     * @param object|string                     $object
     * @param string                            $data
     * @param string                            $contentType
     * @param DenormalizerContextInterface|null $context
     * @param string                            $path
     *
     * @return object
     */
    public function deserialize(
        $object,
        string $data,
        string $contentType,
        DenormalizerContextInterface $context = null,
        string $path = ''
    ) {
        return $this->denormalizer->denormalize($object, $this->decoder->decode($data, $contentType), $context, $path);
    }

    /**
     * @return string[]
     */
    public function getContentTypes(): array
    {
        return $this->decoder->getContentTypes();
    }

    /**
     * @param string $data
     * @param string $contentType
     *
     * @throws DeserializerLogicException
     * @throws DeserializerRuntimeException
     *
     * @return array
     */
    public function decode(string $data, string $contentType): array
    {
        return $this->decoder->decode($data, $contentType);
    }

    /**
     * @param object|string                     $object
     * @param array                             $data
     * @param DenormalizerContextInterface|null $context
     * @param string                            $path
     *
     * @throws DeserializerLogicException
     * @throws DeserializerRuntimeException
     *
     * @return object
     */
    public function denormalize($object, array $data, DenormalizerContextInterface $context = null, string $path = '')
    {
        return $this->denormalizer->denormalize($object, $data, $context, $path);
    }
}
