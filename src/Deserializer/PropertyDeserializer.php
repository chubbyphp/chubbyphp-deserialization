<?php

declare(strict_types=1);

namespace Chubbyphp\Deserialize\Deserializer;

use Chubbyphp\Deserialize\DeserializerInterface;

final class PropertyDeserializer implements PropertyDeserializerInterface
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
    ) {
        return $serializedValue;
    }
}
