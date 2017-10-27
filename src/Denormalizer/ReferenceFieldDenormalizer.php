<?php

declare(strict_types=1);

namespace Chubbyphp\Deserialization\Denormalizer;

use Chubbyphp\Deserialization\Accessor\AccessorInterface;
use Chubbyphp\Deserialization\DeserializerLogicException;
use Chubbyphp\Deserialization\DeserializerRuntimeException;

final class ReferenceFieldDenormalizer implements FieldDenormalizerInterface
{
    /**
     * @var string
     */
    private $class;

    /**
     * @var callable|null
     */
    private $repository;

    /**
     * @var AccessorInterface
     */
    private $accessor;

    /**
     * @param string            $class
     * @param callable|null     $repository
     * @param AccessorInterface $accessor
     */
    public function __construct($class, callable $repository = null, AccessorInterface $accessor)
    {
        $this->class = $class;
        $this->repository = $repository;
        $this->accessor = $accessor;
    }

    /**
     * @param string                       $path
     * @param object                       $object
     * @param mixed                        $value
     * @param DenormalizerContextInterface $context
     * @param DenormalizerInterface|null   $denormalizer
     */
    public function denormalizeField(
        string $path,
        $object,
        $value,
        DenormalizerContextInterface $context,
        DenormalizerInterface $denormalizer = null
    ) {
        if (null === $denormalizer) {
            throw DeserializerLogicException::createMissingDenormalizer($path);
        }

        if (is_array($value)) {
            $existingValue = $this->accessor->getValue($object) ?? $this->class;

            $this->accessor->setValue($object, $denormalizer->denormalize($existingValue, $value, $context));

            return;
        }

        if (is_string($value)) {
            if (null === $repository = $this->repository) {
                throw DeserializerRuntimeException::createInvalidDataType($path, gettype($value), 'array');
            }

            $this->accessor->setValue($object, $repository($this->class, $value));

            return;
        }

        throw DeserializerRuntimeException::createInvalidDataType($path, gettype($value), 'array|string');
    }
}
