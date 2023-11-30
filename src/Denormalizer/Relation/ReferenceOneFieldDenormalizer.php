<?php

declare(strict_types=1);

namespace Chubbyphp\Deserialization\Denormalizer\Relation;

use Chubbyphp\Deserialization\Accessor\AccessorInterface;
use Chubbyphp\Deserialization\Denormalizer\DenormalizerContextInterface;
use Chubbyphp\Deserialization\Denormalizer\DenormalizerInterface;
use Chubbyphp\Deserialization\Denormalizer\FieldDenormalizerInterface;
use Chubbyphp\Deserialization\DeserializerRuntimeException;

final class ReferenceOneFieldDenormalizer implements FieldDenormalizerInterface
{
    /**
     * @var callable
     */
    private $repository;

    public function __construct(
        callable $repository,
        private AccessorInterface $accessor,
        private bool $emptyToNull = false
    ) {
        $this->repository = $repository;
    }

    /**
     * @throws DeserializerRuntimeException
     */
    public function denormalizeField(
        string $path,
        object $object,
        mixed $value,
        DenormalizerContextInterface $context,
        ?DenormalizerInterface $denormalizer = null
    ): void {
        if ('' === $value && $this->emptyToNull) {
            $this->accessor->setValue($object, null);

            return;
        }

        if (null === $value) {
            $this->accessor->setValue($object, null);

            return;
        }

        if (!\is_string($value)) {
            throw DeserializerRuntimeException::createInvalidDataType($path, \gettype($value), 'string');
        }

        $this->accessor->setValue($object, ($this->repository)($value) ?? $value);
    }
}
