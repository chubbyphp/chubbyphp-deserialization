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

    /**
     * @var AccessorInterface
     */
    private $accessor;

    /**
     * @param callable          $repository
     * @param AccessorInterface $accessor
     */
    public function __construct(callable $repository, AccessorInterface $accessor)
    {
        $this->repository = $repository;
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
        if (!is_array($value)) {
            throw DeserializerRuntimeException::createInvalidDataType($path, gettype($value), 'array');
        }

        $repository = $this->repository;

        $newChildObjects = [];
        foreach ($value as $i => $subValue) {
            $subPath = $path.'['.$i.']';

            if (!is_string($subValue)) {
                throw DeserializerRuntimeException::createInvalidDataType($subPath, gettype($subValue), 'string');
            }

            $newChildObjects[$i] = $repository($subValue);
        }

        $this->accessor->setValue($object, $newChildObjects);
    }
}
