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

    /**
     * @var AccessorInterface
     */
    private $accessor;

    /**
     * @var bool
     */
    private $emptyToNull;

    /**
     * @param callable          $repository
     * @param AccessorInterface $accessor
     * @param bool              $emptyToNull
     */
    public function __construct(callable $repository, AccessorInterface $accessor, bool $emptyToNull = false)
    {
        $this->repository = $repository;
        $this->accessor = $accessor;
        $this->emptyToNull = $emptyToNull;
    }

    /**
     * @param string                       $path
     * @param object                       $object
     * @param mixed                        $value
     * @param DenormalizerContextInterface $context
     * @param DenormalizerInterface|null   $denormalizer
     *
     * @throws DeserializerRuntimeException
     */
    public function denormalizeField(
        string $path,
        $object,
        $value,
        DenormalizerContextInterface $context,
        DenormalizerInterface $denormalizer = null
    ): void {
        if ('' === $value && $this->emptyToNull) {
            $this->accessor->setValue($object, null);

            return;
        }

        if (null === $value) {
            $this->accessor->setValue($object, null);

            return;
        }

        if (!is_string($value)) {
            throw DeserializerRuntimeException::createInvalidDataType($path, gettype($value), 'string');
        }

        $repository = $this->repository;

        $this->accessor->setValue($object, $repository($value) ?? $value);
    }
}
