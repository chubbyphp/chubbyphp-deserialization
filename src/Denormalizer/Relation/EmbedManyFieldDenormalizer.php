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

    public function __construct(string $class, AccessorInterface $accessor)
    {
        $this->class = $class;
        $this->accessor = $accessor;
    }

    /**
     * @param mixed $value
     *
     * @throws DeserializerLogicException
     * @throws DeserializerRuntimeException
     */
    public function denormalizeField(
        string $path,
        object $object,
        $value,
        DenormalizerContextInterface $context,
        ?DenormalizerInterface $denormalizer = null
    ): void {
        if (null === $value) {
            $value = [];
        }

        if (null === $denormalizer) {
            throw DeserializerLogicException::createMissingDenormalizer($path);
        }

        if (!is_array($value)) {
            throw DeserializerRuntimeException::createInvalidDataType($path, gettype($value), 'array');
        }

        /** @var array<int|string, object> $relatedObjects */
        $relatedObjects = $this->accessor->getValue($object) ?? [];

        $existEmbObjects = $this->cleanRelatedObjects($relatedObjects);
        $this->assignRelatedObjects($path, $value, $relatedObjects, $existEmbObjects, $context, $denormalizer);

        $this->accessor->setValue($object, $relatedObjects);
    }

    /**
     * @param array<int|string, object> $relatedObjects
     *
     * @return array<int|string, object>
     */
    private function cleanRelatedObjects(&$relatedObjects): array
    {
        $existEmbObjects = [];
        foreach ($relatedObjects as $key => $existEmbObject) {
            $existEmbObjects[$key] = $existEmbObject;
            unset($relatedObjects[$key]);
        }

        return $existEmbObjects;
    }

    /**
     * @param array<int|string, array|null> $value
     * @param array<int|string, object>     $relatedObjects
     * @param array<int|string, object>     $existEmbObjects
     */
    private function assignRelatedObjects(
        string $path,
        array $value,
        &$relatedObjects,
        array $existEmbObjects,
        DenormalizerContextInterface $context,
        DenormalizerInterface $denormalizer
    ): void {
        foreach ($value as $key => $subValue) {
            $subPath = $path.'['.$key.']';

            if (null === $subValue) {
                $subValue = [];
            }

            if (!is_array($subValue)) {
                throw DeserializerRuntimeException::createInvalidDataType($subPath, gettype($subValue), 'array');
            }

            $relatedObject = $existEmbObjects[$key] ?? $this->class;

            $relatedObjects[$key] = $denormalizer->denormalize($relatedObject, $subValue, $context, $subPath);
        }
    }
}
