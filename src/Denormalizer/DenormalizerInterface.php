<?php

declare(strict_types=1);

namespace Chubbyphp\Deserialization\Denormalizer;

use Chubbyphp\Deserialization\DeserializerLogicException;
use Chubbyphp\Deserialization\DeserializerRuntimeException;

interface DenormalizerInterface
{
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
    ): object;
}
