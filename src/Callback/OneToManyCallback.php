<?php

declare(strict_types=1);

namespace Chubbyphp\Deserialize\Callback;

use Chubbyphp\Deserialize\DeserializerInterface;
use Chubbyphp\Model\Collection\ModelCollectionInterface;

final class OneToManyCallback implements CallbackInterface
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
     * @param DeserializerInterface    $deserializer
     * @param array                    $serializedValues
     * @param ModelCollectionInterface $collection
     * @param object $object
     * @return ModelCollectionInterface
     */
    public function __invoke(DeserializerInterface $deserializer, $serializedValues, $collection, $object)
    {
        $oldValues = $collection->getModels();

        $newValues = [];
        foreach ($serializedValues as $i => $serializedValue) {
            if (isset($oldValues[$i])) {
                $value = $deserializer->deserializeByObject($serializedValue, $oldValues[$i]);

                unset($oldValues[$i]);
            } else {
                $value = $deserializer->deserializeByClass($serializedValue, $this->manyClass);
            }

            $newValues[$i] = $value;
        }

        $collection->setModels($newValues);

        return $collection;
    }
}
