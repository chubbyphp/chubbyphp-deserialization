<?php

declare(strict_types=1);

namespace Chubbyphp\Deserialization\Denormalizer\Relation;

use Chubbyphp\Deserialization\Accessor\AccessorInterface;
use Chubbyphp\Deserialization\Denormalizer\DenormalizerContextInterface;
use Chubbyphp\Deserialization\Denormalizer\DenormalizerInterface;
use Chubbyphp\Deserialization\Denormalizer\FieldDenormalizerInterface;
use Chubbyphp\Deserialization\DeserializerLogicException;
use Chubbyphp\Deserialization\DeserializerRuntimeException;

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
     * @var callable|null
     */
    private $collectionFactory;

    /**
     * @param string            $class
     * @param AccessorInterface $accessor
     * @param callable|null     $collectionFactory
     */
    public function __construct(string $class, AccessorInterface $accessor, callable $collectionFactory = null)
    {
        $this->class = $class;
        $this->accessor = $accessor;
        $this->collectionFactory = $collectionFactory;
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

        $existEmbObjects = $this->accessor->getValue($object);

        $relatedObjects = $this->createCollection();
        foreach ($value as $i => $subValue) {
            $subPath = $path.'['.$i.']';

            if (!is_array($subValue)) {
                throw DeserializerRuntimeException::createInvalidDataType($subPath, gettype($subValue), 'array');
            }

            $relatedObject = $existEmbObjects[$i] ?? $this->class;

            $relatedObjects[$i] = $denormalizer->denormalize($relatedObject, $subValue, $context, $subPath);
        }

        $this->accessor->setValue($object, $relatedObjects);
    }

    /**
     * @return array|\Traversable|\ArrayAccess
     */
    private function createCollection()
    {
        if (null === $this->collectionFactory) {
            return [];
        }

        $collectionFactory = $this->collectionFactory;

        return $collectionFactory();
    }
}
