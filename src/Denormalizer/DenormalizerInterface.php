<?php

declare(strict_types=1);

namespace Chubbyphp\Deserialization\Denormalizer;

use Chubbyphp\Deserialization\DeserializerLogicException;
use Chubbyphp\Deserialization\DeserializerRuntimeException;

interface DenormalizerInterface
{
    /**
     * @param object|string $object
     * @param array<mixed>  $data
     *
     * @throws DeserializerLogicException
     * @throws DeserializerRuntimeException
     *
     * @return object
     */
    public function denormalize($object, array $data, DenormalizerContextInterface $context = null, string $path = '');
}
