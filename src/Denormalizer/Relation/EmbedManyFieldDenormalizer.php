<?php

declare(strict_types=1);

namespace Chubbyphp\Deserialization\Denormalizer\Relation;

use Chubbyphp\Deserialization\Accessor\AccessorInterface;
use Chubbyphp\Deserialization\Denormalizer\DenormalizerContextInterface;
use Chubbyphp\Deserialization\Denormalizer\DenormalizerInterface;
use Chubbyphp\Deserialization\Denormalizer\FieldDenormalizerInterface;
use Chubbyphp\Deserialization\DeserializerLogicException;
use Chubbyphp\Deserialization\DeserializerRuntimeException;
use Doctrine\Common\Persistence\Proxy;

final class EmbedManyFieldDenormalizer implements FieldDenormalizerInterface
{
    /**
     * @var string
     */
    private $class;

    /**
     * @var AccessorInterface
     */
    private $accessor;

    /**
     * @var string|null
     */
    private $collectionClass;

    /**
     * @param string            $class
     * @param AccessorInterface $accessor
     * @param string|null       $collectionClass
     */
    public function __construct(string $class, AccessorInterface $accessor, string $collectionClass = null)
    {
        $this->class = $class;
        $this->accessor = $accessor;
        $this->collectionClass = $collectionClass;
    }

    /**
     * @param string                       $path
     * @param object                       $object
     * @param mixed                        $value
     * @param DenormalizerContextInterface $context
     * @param DenormalizerInterface|null   $denormalizer
     *
     * @throws DeserializerLogicException
     * @throws DeserializerRuntimeException
     */
    public function denormalizeField(
        string $path,
        $object,
        $value,
        DenormalizerContextInterface $context,
        DenormalizerInterface $denormalizer = null
    ) {
        if (null === $value) {
            $this->accessor->setValue($object, $value);

            return;
        }

        if (null === $denormalizer) {
            throw DeserializerLogicException::createMissingDenormalizer($path);
        }

        if (!is_array($value)) {
            throw DeserializerRuntimeException::createInvalidDataType($path, gettype($value), 'array');
        }

        $existingEmbeddedObjects = $this->accessor->getValue($object) ?? [];

        $embeddedObjects = $this->newCollection();
        foreach ($value as $i => $subValue) {
            $subPath = $path.'['.$i.']';

            if (!is_array($subValue)) {
                throw DeserializerRuntimeException::createInvalidDataType($subPath, gettype($subValue), 'array');
            }

            $embeddedObject = $this->getEmbeddedObjectOrClass($i, $existingEmbeddedObjects);

            $embeddedObjects[$i] = $denormalizer->denormalize($embeddedObject, $subValue, $context, $subPath);
        }

        $this->accessor->setValue($object, $embeddedObjects);
    }

    /**
     * @param string|int         $i
     * @param array|\Traversable $existingEmbeddedObjects
     *
     * @return object|string
     */
    private function getEmbeddedObjectOrClass($i, $existingEmbeddedObjects)
    {
        if (isset($existingEmbeddedObjects[$i])) {
            $embeddedObject = $existingEmbeddedObjects[$i];

            if (interface_exists('Doctrine\Common\Persistence\Proxy')
                && $embeddedObject instanceof Proxy && !$embeddedObject->__isInitialized()
            ) {
                $embeddedObject->__load();
            }

            return $embeddedObject;
        }

        return $this->class;
    }

    /**
     * @return array|\ArrayAccess|\Traversable
     */
    private function newCollection()
    {
        if (null === $this->collectionClass) {
            return [];
        }

        $collectionClass = $this->collectionClass;

        return new $collectionClass();
    }
}
