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
     * @param string            $class
     * @param AccessorInterface $accessor
     */
    public function __construct(string $class, AccessorInterface $accessor)
    {
        $this->class = $class;
        $this->accessor = $accessor;
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

        $relatedObjects = $this->accessor->getValue($object) ?? [];

        $existEmbObjects = $this->cleanRelatedObjects($relatedObjects);
        $this->assignRelatedObjects($path, $value, $relatedObjects, $existEmbObjects, $context, $denormalizer);

        $this->accessor->setValue($object, $relatedObjects);
    }

    /**
     * @param iterable $relatedObjects
     *
     * @return array
     */
    private function cleanRelatedObjects(&$relatedObjects)
    {
        $existEmbObjects = [];
        foreach ($relatedObjects as $key => $existEmbObject) {
            $existEmbObjects[$key] = $existEmbObject;
            unset($relatedObjects[$key]);
        }

        return $existEmbObjects;
    }

    /**
     * @param string                          $path
     * @param array                           $value
     * @param iterable $relatedObjects
     * @param array                           $existEmbObjects
     * @param DenormalizerContextInterface    $context
     * @param DenormalizerInterface           $denormalizer
     */
    private function assignRelatedObjects(
        string $path,
        array $value,
        &$relatedObjects,
        array $existEmbObjects,
        DenormalizerContextInterface $context,
        DenormalizerInterface $denormalizer
    ) {
        foreach ($value as $key => $subValue) {
            $subPath = $path.'['.$key.']';

            if (!is_array($subValue)) {
                throw DeserializerRuntimeException::createInvalidDataType($subPath, gettype($subValue), 'array');
            }

            $relatedObject = $existEmbObjects[$key] ?? $this->class;

            $relatedObjects[$key] = $denormalizer->denormalize($relatedObject, $subValue, $context, $subPath);
        }
    }
}
