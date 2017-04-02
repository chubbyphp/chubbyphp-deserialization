<?php

declare(strict_types=1);

namespace Chubbyphp\Deserialize\Callback\Simple;

use Chubbyphp\Deserialize\Callback\CallbackInterface;
use Chubbyphp\Deserialize\DeserializerInterface;

final class BidirectionalOneToManyCallback implements CallbackInterface
{
    /**
     * @var string
     */
    private $manyClass;

    /**
     * @var string
     */
    private $bidirectionalProperty;

    /**
     * @param string $manyClass
     * @param string $bidirectionalProperty
     */
    public function __construct(string $manyClass, string $bidirectionalProperty)
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
}
