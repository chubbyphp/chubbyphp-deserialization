<?php

declare(strict_types=1);

namespace Chubbyphp\Deserialize\Deserializer;

use Chubbyphp\Deserialize\DeserializerInterface;

final class PropertyDeserializerCallback implements PropertyDeserializerInterface
{
    /**
     * @var callable
     */
    private $callback;

    /**
     * @param callable $callback
     */
    public function __construct(callable $callback)
    {
        $this->callback = $callback;
    }

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
    ) {
        $callback = $this->callback;

        return $callback($serializedValue, $existingValue, $object, $deserializer);
    }
}
