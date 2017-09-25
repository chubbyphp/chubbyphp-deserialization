<?php

declare(strict_types=1);

namespace Chubbyphp\Deserialization\Denormalizer;

interface DenormalizerInterface
{
    /**
     * @param object|string                     $object
     * @param array                             $data
     * @param DenormalizerContextInterface|null $context
     * @param string                            $path
     *
     * @return object
     *
     * @throws DenormalizerException
     */
    public function denormalize($object, array $data, DenormalizerContextInterface $context = null, string $path = '');
}
