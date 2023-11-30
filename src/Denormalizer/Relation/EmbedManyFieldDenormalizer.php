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
    public function __construct(private string $class, private AccessorInterface $accessor) {}

    /**
     * @throws DeserializerLogicException
     * @throws DeserializerRuntimeException
     */
    public function denormalizeField(
        string $path,
        object $object,
        mixed $value,
        DenormalizerContextInterface $context,
        ?DenormalizerInterface $denormalizer = null
    ): void {
        if (null === $value) {
            $value = [];
        }

        if (null === $denormalizer) {
            throw DeserializerLogicException::createMissingDenormalizer($path);
        }

        if (!\is_array($value)) {
            throw DeserializerRuntimeException::createInvalidDataType($path, \gettype($value), 'array');
        }

        /** @var array<int|string, object>|\ArrayAccess<int|string, object> $relatedObjects */
        $relatedObjects = $this->accessor->getValue($object) ?? [];

        $existEmbObjects = $this->cleanRelatedObjects($relatedObjects);
        $this->assignRelatedObjects($path, $value, $relatedObjects, $existEmbObjects, $context, $denormalizer);

        $this->accessor->setValue($object, $relatedObjects);
    }

    /**
     * @param array<int|string, object>|\ArrayAccess<int|string, object> $relatedObjects
     *
     * @return array<int|string, object>
     */
    private function cleanRelatedObjects(array|\ArrayAccess &$relatedObjects): array
    {
        $existEmbObjects = [];
        foreach ($relatedObjects as $key => $existEmbObject) {
            $existEmbObjects[$key] = $existEmbObject;
            unset($relatedObjects[$key]);
        }

        return $existEmbObjects;
    }

    /**
     * @param array<int|string, null|array>                              $value
     * @param array<int|string, object>|\ArrayAccess<int|string, object> $relatedObjects
     * @param array<int|string, object>                                  $existEmbObjects
     */
    private function assignRelatedObjects(
        string $path,
        array $value,
        array|\ArrayAccess &$relatedObjects,
        array $existEmbObjects,
        DenormalizerContextInterface $context,
        DenormalizerInterface $denormalizer
    ): void {
        foreach ($value as $key => $subValue) {
            $subPath = $path.'['.$key.']';

            if (null === $subValue) {
                $subValue = [];
            }

            if (!\is_array($subValue)) {
                throw DeserializerRuntimeException::createInvalidDataType($subPath, \gettype($subValue), 'array');
            }

            $relatedObject = $existEmbObjects[$key] ?? $this->class;

            $relatedObjects[$key] = $denormalizer->denormalize($relatedObject, $subValue, $context, $subPath);
        }
    }
}
