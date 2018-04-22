<?php

declare(strict_types=1);

namespace Chubbyphp\DeserializationDoctrine\Deserializer;

use Chubbyphp\Deserialization\Deserializer\PropertyDeserializerInterface;
use Chubbyphp\Deserialization\DeserializerInterface;
use Doctrine\Common\Collections\Collection;

final class PropertyModelCollectionDeserializer implements PropertyDeserializerInterface
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
     * @param string                $path
     * @param array                 $serializedValues
     * @param Collection|null       $collection
     * @param object                $object
     * @param DeserializerInterface $deserializer
     *
     * @return Collection
     */
    public function deserializeProperty(
        string $path,
        $serializedValues,
        $collection = null,
        $object = null,
        DeserializerInterface $deserializer = null
    ) {
        $this->collectionOrException($collection);
        $this->deserializerOrException($deserializer);

        $oldValues = $collection->getValues();

        $collection->clear();

        foreach ($serializedValues as $i => $serializedValue) {
            $subPath = $path.'['.$i.']';
            if (isset($oldValues[$i])) {
                $value = $deserializer->deserializeByObject($serializedValue, $oldValues[$i], $subPath);
            } else {
                $value = $deserializer->deserializeByClass($serializedValue, $this->manyClass, $subPath);
            }

            $collection->set($i, $value);
        }

        return $collection;
    }

    /**
     * @param Collection|null $collection
     *
     * @throws \RuntimeException
     */
    private function collectionOrException($collection)
    {
        if (!$collection instanceof Collection) {
            throw new \RuntimeException(
                sprintf(
                    'Object needs to implement: %s, given: %s',
                    Collection::class,
                    is_object($collection) ? get_class($collection) : gettype($collection)
                )
            );
        }
    }

    /**
     * @param DeserializerInterface|null $deserializer
     *
     * @throws \RuntimeException
     */
    private function deserializerOrException(DeserializerInterface $deserializer = null)
    {
        if (null === $deserializer) {
            throw new \RuntimeException(sprintf('Deserializer needed: %s', DeserializerInterface::class));
        }
    }
}
