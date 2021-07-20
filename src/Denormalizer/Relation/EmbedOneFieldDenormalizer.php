<?php

declare(strict_types=1);

namespace Chubbyphp\Deserialization\Denormalizer\Relation;

use Chubbyphp\Deserialization\Accessor\AccessorInterface;
use Chubbyphp\Deserialization\Denormalizer\DenormalizerContextInterface;
use Chubbyphp\Deserialization\Denormalizer\DenormalizerInterface;
use Chubbyphp\Deserialization\Denormalizer\FieldDenormalizerInterface;
use Chubbyphp\Deserialization\DeserializerLogicException;
use Chubbyphp\Deserialization\DeserializerRuntimeException;

final class EmbedOneFieldDenormalizer implements FieldDenormalizerInterface
{
    private string $class;

    private AccessorInterface $accessor;

    private ?AccessorInterface $parentAccessor;

    public function __construct(string $class, AccessorInterface $accessor, ?AccessorInterface $parentAccessor = null)
    {
        $this->class = $class;
        $this->accessor = $accessor;
        $this->parentAccessor = $parentAccessor;
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
        ?DenormalizerInterface $denormalizer = null,
        bool $hasReverseOwning = false
    ): void {
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

        $relatedObject = $this->accessor->getValue($object) ?? $this->class;

        $denormalizedRelatedObject = $denormalizer->denormalize($relatedObject, $value, $context, $path);

        $this->accessor->setValue($object, $denormalizedRelatedObject);

        if (true === $hasReverseOwning) {
            if (null === $this->parentAccessor) {
                throw DeserializerLogicException::createMissingParentAccessor($path);
            }

            $this->parentAccessor->setValue($denormalizedRelatedObject, $object);
        }
    }
}
