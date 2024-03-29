<?php

declare(strict_types=1);

namespace Chubbyphp\Deserialization;

use Chubbyphp\DecodeEncode\Decoder\DecoderInterface;
use Chubbyphp\Deserialization\Denormalizer\DenormalizerContextInterface;
use Chubbyphp\Deserialization\Denormalizer\DenormalizerInterface;

interface DeserializerInterface extends DecoderInterface, DenormalizerInterface
{
    public function deserialize(
        object|string $object,
        string $data,
        string $contentType,
        ?DenormalizerContextInterface $context = null,
        string $path = ''
    ): object;
}
