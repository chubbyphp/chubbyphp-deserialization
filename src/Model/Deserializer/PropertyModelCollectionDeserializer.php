<?php

declare(strict_types=1);

namespace Chubbyphp\DeserializationModel\Deserializer;

use Chubbyphp\Deserialization\Deserializer\PropertyDeserializerInterface;
use Chubbyphp\Deserialization\DeserializerInterface;
use Chubbyphp\Model\Collection\ModelCollectionInterface;

final class PropertyModelCollectionDeserializer implements PropertyDeserializerInterface
{
    /**
     * @var string
     */
    private $manyClass;

    /**
     * @var bool
     */
    private $replace;

    /**
     * @param string $manyClass
     * @param bool   $replace
     */
    public function __construct(string $manyClass, bool $replace = false)
    {
        $this->manyClass = $manyClass;
        $this->replace = $replace;
    }

    /**
     * @param string                        $path
     * @param array                         $serializedValues
     * @param ModelCollectionInterface|null $collection
     * @param object                        $object
     * @param DeserializerInterface         $deserializer
     *
     * @return ModelCollectionInterface
     */
    public function deserializeProperty(
        string $path,
        $serializedValues,
        $collection = null,
        $object = null,
        DeserializerInterface $deserializer = null
    ) {
        $this->modelCollectionOrException($collection);
        $this->deserializerOrException($deserializer);

        $oldValues = $collection->getModels();

        $newValues = [];
        foreach ($serializedValues as $i => $serializedValue) {
            $subPath = $path.'['.$i.']';
            if (!$this->replace && isset($oldValues[$i])) {
                $value = $deserializer->deserializeByObject($serializedValue, $oldValues[$i], $subPath);
            } else {
                $value = $deserializer->deserializeByClass($serializedValue, $this->manyClass, $subPath);
            }

            $newValues[$i] = $value;
        }

        $collection->setModels($newValues);

        return $collection;
    }

    /**
     * @param ModelCollectionInterface|null $collection
     *
     * @throws \RuntimeException
     */
    private function modelCollectionOrException($collection)
    {
        if (!$collection instanceof ModelCollectionInterface) {
            throw new \RuntimeException(
                sprintf(
                    'Object needs to implement: %s, given: %s',
                    ModelCollectionInterface::class,
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
