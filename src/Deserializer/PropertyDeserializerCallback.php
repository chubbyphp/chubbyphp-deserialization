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
        $callback = $this->callback;

        return $callback($path, $serializedValue, $existingValue, $object, $deserializer);
    }
}
