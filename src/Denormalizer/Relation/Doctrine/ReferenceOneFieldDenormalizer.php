<?php

declare(strict_types=1);

namespace Chubbyphp\Deserialization\Denormalizer\Relation\Doctrine;

use Chubbyphp\Deserialization\Accessor\AccessorInterface;
use Chubbyphp\Deserialization\Denormalizer\DenormalizerContextInterface;
use Chubbyphp\Deserialization\Denormalizer\DenormalizerInterface;
use Chubbyphp\Deserialization\Denormalizer\FieldDenormalizerInterface;
use Chubbyphp\Deserialization\DeserializerRuntimeException;
use Doctrine\Common\Persistence\Proxy;

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

        if (!is_string($value)) {
            throw DeserializerRuntimeException::createInvalidDataType($path, gettype($value), 'string');
        }

        $repository = $this->repository;

        $refObject = $repository($value);

        $this->resolveProxy($refObject);

        $this->accessor->setValue($object, $refObject);
    }

    private function resolveProxy($refObject)
    {
        if (null !== $refObject && $refObject instanceof Proxy && !$refObject->__isInitialized()) {
            $refObject->__load();
        }
    }
}
