<?php

declare(strict_types=1);

namespace Chubbyphp\Deserialization\Denormalizer\Relation;

use Chubbyphp\Deserialization\Accessor\AccessorInterface;
use Chubbyphp\Deserialization\Denormalizer\DenormalizerContextInterface;
use Chubbyphp\Deserialization\Denormalizer\DenormalizerInterface;
use Chubbyphp\Deserialization\Denormalizer\FieldDenormalizerInterface;
use Chubbyphp\Deserialization\DeserializerLogicException;
use Chubbyphp\Deserialization\DeserializerRuntimeException;

final class ReferenceManyFieldDenormalizer implements FieldDenormalizerInterface
{
    /**
     * @var callable
     */
    private $repository;

    public function __construct(callable $repository, private AccessorInterface $accessor)
    {
        $this->repository = $repository;
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

        if (!\is_array($value)) {
            throw DeserializerRuntimeException::createInvalidDataType($path, \gettype($value), 'array');
        }

        /** @var array<int|string, object>|\ArrayAccess<int|string, object> $relatedObjects */
        $relatedObjects = $this->accessor->getValue($object) ?? [];

        $this->cleanRelatedObjects($relatedObjects);
        $this->assignRelatedObjects($path, $value, $relatedObjects);

        $this->accessor->setValue($object, $relatedObjects);
    }

    /**
     * @param array<int|string, object>|\ArrayAccess<int|string, object> $relatedObjects
     */
    private function cleanRelatedObjects(array|\ArrayAccess &$relatedObjects): void
    {
        foreach ($relatedObjects as $key => $existEmbObject) {
            unset($relatedObjects[$key]);
        }
    }

    /**
     * @param array<int|string, string>                                  $value
     * @param array<int|string, object>|\ArrayAccess<int|string, object> $relatedObjects
     */
    private function assignRelatedObjects(string $path, array $value, array|\ArrayAccess &$relatedObjects): void
    {
        foreach ($value as $key => $subValue) {
            $subPath = $path.'['.$key.']';

            if (!\is_string($subValue)) {
                throw DeserializerRuntimeException::createInvalidDataType($subPath, \gettype($subValue), 'string');
            }

            $relatedObjects[$key] = ($this->repository)($subValue) ?? $subValue;
        }
    }
}
