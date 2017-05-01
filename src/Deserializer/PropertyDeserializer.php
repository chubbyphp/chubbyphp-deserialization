<?php

declare(strict_types=1);

namespace Chubbyphp\Deserialize\Deserializer;

use Chubbyphp\Deserialize\DeserializerInterface;

final class PropertyDeserializer implements PropertyDeserializerInterface
{
    /**
     * @param DeserializerInterface|null $deserializer
     * @param mixed $serializedValue
     * @param mixed $existingValue
     * @param object $object
     * @return mixed
     */
    public function deserializeProperty(
        $serializedValue,
        $existingValue = null,
        $object = null,
        DeserializerInterface $deserializer = null
    ) {
        return $serializedValue;
    }
}
