<?php

declare(strict_types=1);

namespace Chubbyphp\Deserialize\Deserialize;

use Chubbyphp\Deserialize\DeserializerInterface;

final class PropertyDeserialize implements PropertyDeserializeInterface
{
    /**
     * @param DeserializerInterface $deserializer
     * @param mixed $serializedValue
     * @param mixed$oldValue
     * @param object $object
     * @return mixed
     */
    public function deserializeProperty(DeserializerInterface $deserializer, $serializedValue, $oldValue, $object)
    {
        return $serializedValue;
    }
}
