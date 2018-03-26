<?php

declare(strict_types=1);

namespace Chubbyphp\Deserialization\Denormalizer\Relation;

use Chubbyphp\Deserialization\Accessor\AccessorInterface;
use Chubbyphp\Deserialization\Denormalizer\DenormalizerContextInterface;
use Chubbyphp\Deserialization\Denormalizer\DenormalizerInterface;
use Chubbyphp\Deserialization\Denormalizer\FieldDenormalizerInterface;
use Chubbyphp\Deserialization\DeserializerLogicException;
use Chubbyphp\Deserialization\DeserializerRuntimeException;
use Doctrine\Common\Persistence\Proxy;

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
     * @var string|null
     */
    private $collectionClass;

    /**
     * @param callable          $repository
     * @param AccessorInterface $accessor
     * @param string|null       $collectionClass
     */
    public function __construct(callable $repository, AccessorInterface $accessor, string $collectionClass = null)
    {
        $this->repository = $repository;
        $this->accessor = $accessor;
        $this->collectionClass = $collectionClass;
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
        if (null === $value) {
            $this->accessor->setValue($object, $value);

            return;
        }

        if (!is_array($value)) {
            throw DeserializerRuntimeException::createInvalidDataType($path, gettype($value), 'array');
        }

        $repository = $this->repository;

        $referencedObjects = $this->newCollection();
        foreach ($value as $i => $subValue) {
            $subPath = $path.'['.$i.']';

            if (!is_string($subValue)) {
                throw DeserializerRuntimeException::createInvalidDataType($subPath, gettype($subValue), 'string');
            }

            $referencedObject = $repository($subValue);

            if (null !== $referencedObject) {
                if (interface_exists('Doctrine\Common\Persistence\Proxy')
                    && $referencedObject instanceof Proxy && !$referencedObject->__isInitialized()
                ) {
                    $referencedObject->__load();
                }
            }

            $referencedObjects[$i] = $referencedObject;
        }

        $this->accessor->setValue($object, $referencedObjects);
    }

    /**
     * @return array|\ArrayAccess|\Traversable
     */
    private function newCollection()
    {
        if (null === $this->collectionClass) {
            return [];
        }

        $collectionClass = $this->collectionClass;

        return new $collectionClass();
    }
}
