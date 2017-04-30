<?php

declare(strict_types=1);

namespace Chubbyphp\Deserialize\Deserialize;

use Chubbyphp\Deserialize\DeserializerInterface;

final class PropertyDeserializeCallback implements PropertyDeserializeInterface
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
     * @param DeserializerInterface $deserializer
     * @param mixed $serializedValue
     * @param mixed$oldValue
     * @param object $object
     * @return mixed
     */
    public function deserializeProperty(DeserializerInterface $deserializer, $serializedValue, $oldValue, $object)
    {
        $callback = $this->callback;

        return $callback($deserializer, $serializedValue, $oldValue, $object);
    }
}
