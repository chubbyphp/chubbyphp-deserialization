<?php

declare(strict_types=1);

namespace Chubbyphp\Deserialization\Denormalizer;

use Chubbyphp\Deserialization\DeserializerLogicException;
use Chubbyphp\Deserialization\DeserializerRuntimeException;

interface DenormalizerInterface
{
    /**
     * @param object|string $object
     *
     * @throws DeserializerLogicException
     * @throws DeserializerRuntimeException
     *
     * @return object
     */
    public function denormalize($object, array $data, DenormalizerContextInterface $context = null, string $path = '');
}
