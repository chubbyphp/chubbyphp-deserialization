<?php

declare(strict_types=1);

namespace Chubbyphp\Deserialization\Deserializer;

use Chubbyphp\Deserialization\DeserializerInterface;

interface PropertyDeserializerInterface
{
    /**
     * @param string                     $path
     * @param mixed                      $serializedValue
     * @param mixed                      $existingValue
     * @param object                     $object
     * @param DeserializerInterface|null $deserializer
     *
     * @return mixed
     */
    public function deserializeProperty(
        string $path,
        $serializedValue,
        $existingValue = null,
        $object = null,
        DeserializerInterface $deserializer = null
    );
}
