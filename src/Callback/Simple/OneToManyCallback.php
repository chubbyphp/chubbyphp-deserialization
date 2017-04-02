<?php

declare(strict_types=1);

namespace Chubbyphp\Deserialize\Callback\Simple;

use Chubbyphp\Deserialize\Callback\CallbackInterface;
use Chubbyphp\Deserialize\DeserializerInterface;

final class OneToManyCallback implements CallbackInterface
{
    /**
     * @var string
     */
    private $manyClass;

    /**
     * @var string|null
     */
    private $bidirectionalProperty;

    /**
     * @param string $manyClass
     * @param null|string $bidirectionalProperty
     */
    public function __construct(string $manyClass, string $bidirectionalProperty = null)
    {
        $this->manyClass = $manyClass;
        $this->bidirectionalProperty = $bidirectionalProperty;
    }

    /**
     * @param DeserializerInterface $deserializer
     * @param array $serializedValues
     * @param array $oldValues
     * @param object $object
     * @return array
     */
    public function __invoke(DeserializerInterface $deserializer, $serializedValues, $oldValues, $object)
    {
        if (null !== $this->bidirectionalProperty) {
            return $this->bidirectional($deserializer, $serializedValues, $oldValues, $object);
        }

        return $this->unidirectional($deserializer, $serializedValues, $oldValues);
    }

    /**
     * @param DeserializerInterface $deserializer
     * @param array $serializedValues
     * @param array $oldValues
     * @param object $object
     * @return array
     */
    private function bidirectional(DeserializerInterface $deserializer, $serializedValues, $oldValues, $object)
    {
        $newValues = [];
        foreach ($serializedValues as $i => $serializedValue) {
            $serializedValue[$this->bidirectionalProperty] = $object;

            if (isset($oldValues[$i])) {
                $relatedObject = $deserializer->deserializeByObject($serializedValue, $oldValues[$i]);

                unset($oldValues[$i]);
            } else {
                $relatedObject = $deserializer->deserializeByClass($serializedValue, $this->manyClass);
            }

            $newValues[$i] = $relatedObject;
        }

        foreach ($oldValues as $oldValue) {
            $deserializer->deserializeByObject([$this->bidirectionalProperty => null], $oldValue);
        }

        return $newValues;
    }

    /**
     * @param DeserializerInterface $deserializer
     * @param array $serializedValues
     * @param array $oldValues
     * @return array
     */
    private function unidirectional(DeserializerInterface $deserializer, $serializedValues, $oldValues)
    {
        $newValues = [];
        foreach ($serializedValues as $i => $serializedValue) {
            if (isset($oldValues[$i])) {
                $relatedObject = $deserializer->deserializeByObject($serializedValue, $oldValues[$i]);
            } else {
                $relatedObject = $deserializer->deserializeByClass($serializedValue, $this->manyClass);
            }

            $newValues[$i] = $relatedObject;
        }

        return $newValues;
    }
}
