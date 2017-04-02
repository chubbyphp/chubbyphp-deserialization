<?php

declare(strict_types=1);

namespace Chubbyphp\Deserialize\Callback\Simple;

use Chubbyphp\Deserialize\Callback\CallbackInterface;
use Chubbyphp\Deserialize\DeserializerInterface;

final class UnidirectionalOneToManyCallback implements CallbackInterface
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
     * @param array $serializedValues
     * @param array $oldValues
     * @param object $object
     * @return array
     */
    public function __invoke(DeserializerInterface $deserializer, $serializedValues, $oldValues, $object)
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
