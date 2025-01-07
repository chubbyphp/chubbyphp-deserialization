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
    public function __construct(
        private string $class,
        private AccessorInterface $accessor,
        private ?AccessorInterface $parentAccessor = null
    ) {}

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
            $this->accessor->setValue($object, $value);

            return;
        }

        if (!$denormalizer instanceof DenormalizerInterface) {
            throw DeserializerLogicException::createMissingDenormalizer($path);
        }

        if (!\is_array($value)) {
            throw DeserializerRuntimeException::createInvalidDataType($path, \gettype($value), 'array');
        }

        $relatedObject = $this->accessor->getValue($object) ?? $this->class;

        $denormalizedRelatedObject = $denormalizer->denormalize($relatedObject, $value, $context, $path);

        $this->accessor->setValue($object, $denormalizedRelatedObject);

        if ($this->parentAccessor instanceof AccessorInterface) {
            $this->parentAccessor->setValue($denormalizedRelatedObject, $object);
        }
    }
}
