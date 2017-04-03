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
     * @param string $manyClass
     */
    public function __construct(string $manyClass)
    {
        $this->manyClass = $manyClass;
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
            } else {
                $value = $deserializer->deserializeByClass($serializedValue, $this->manyClass);
            }

            $newValues[$i] = $value;
        }

        $collection->setModels($newValues);

        return $collection;
    }
}
