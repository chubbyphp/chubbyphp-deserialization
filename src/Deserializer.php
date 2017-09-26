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
     * @param object|string                $object
     * @param string                       $data
     * @param string                       $contentType
     * @param DenormalizerContextInterface $context
     *
     * @return object
     */
    public function deserialize(
        $object,
        string $data,
        string $contentType,
        DenormalizerContextInterface $context = null
    ) {
        return $this->denormalizer->denormalize($object, $this->decoder->decode($data, $contentType), $context);
    }
}
