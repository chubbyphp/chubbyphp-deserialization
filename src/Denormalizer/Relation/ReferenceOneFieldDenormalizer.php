<?php

declare(strict_types=1);

namespace Chubbyphp\Deserialization\Denormalizer\Relation;

use Chubbyphp\Deserialization\Accessor\AccessorInterface;
use Chubbyphp\Deserialization\Denormalizer\DenormalizerContextInterface;
use Chubbyphp\Deserialization\Denormalizer\DenormalizerInterface;
use Chubbyphp\Deserialization\Denormalizer\FieldDenormalizerInterface;
use Chubbyphp\Deserialization\DeserializerRuntimeException;
use Doctrine\Common\Persistence\Proxy;

/**
 * @deprecated use Basic or Doctrine ReferenceOneFieldDenormalizer
 */
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

        $relatedObject = $repository($value);

        $this->resolveProxy($relatedObject);

        $this->accessor->setValue($object, $relatedObject);
    }

    private function resolveProxy($relatedObject)
    {
        if (null !== $relatedObject && interface_exists('Doctrine\Common\Persistence\Proxy')
            && $relatedObject instanceof Proxy && !$relatedObject->__isInitialized()
        ) {
            $relatedObject->__load();
        }
    }
}
