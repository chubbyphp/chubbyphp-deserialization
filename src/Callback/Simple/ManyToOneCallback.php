<?php

declare(strict_types=1);

namespace Chubbyphp\Deserialize\Callback\Simple;

use Chubbyphp\Deserialize\Callback\CallbackInterface;
use Chubbyphp\Deserialize\DeserializerInterface;

final class ManyToOneCallback implements CallbackInterface
{
    /**
     * @var string
     */
    private $manyClass;

    /**
     * @param string $manyClass
     */
    public function __construct(string $manyClass)
    {
        $this->manyClass = $manyClass;
    }

    /**
     * @param DeserializerInterface $deserializer
     * @param array|object|null $serializedValue
     * @param object|null $oldValue
     * @param object $object
     * @return object|null
     */
    public function __invoke(DeserializerInterface $deserializer, $serializedValue, $oldValue, $object)
    {
        if (is_object($serializedValue)) {
            return $serializedValue;
        }

        if (null === $serializedValue) {
            return null;
        }

        if (null !== $oldValue) {
            return $deserializer->deserializeByObject($serializedValue, $oldValue);
        }

        return $deserializer->deserializeByClass($serializedValue, $this->manyClass);
    }
}
