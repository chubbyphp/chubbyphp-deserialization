<?php

declare(strict_types=1);

namespace Chubbyphp\Deserialize\Deserializer;

use Chubbyphp\Deserialize\DeserializerInterface;

interface PropertyDeserializerInterface
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
        $existingValue,
        $object,
        DeserializerInterface $deserializer = null
    );
}
