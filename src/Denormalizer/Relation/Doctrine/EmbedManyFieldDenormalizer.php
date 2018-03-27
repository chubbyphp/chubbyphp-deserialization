<?php

declare(strict_types=1);

namespace Chubbyphp\Deserialization\Denormalizer\Relation\Doctrine;

use Chubbyphp\Deserialization\Accessor\AccessorInterface;
use Chubbyphp\Deserialization\Denormalizer\DenormalizerContextInterface;
use Chubbyphp\Deserialization\Denormalizer\DenormalizerInterface;
use Chubbyphp\Deserialization\Denormalizer\FieldDenormalizerInterface;
use Chubbyphp\Deserialization\DeserializerLogicException;
use Chubbyphp\Deserialization\DeserializerRuntimeException;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Persistence\Proxy;

final class EmbedManyFieldDenormalizer implements FieldDenormalizerInterface
{
    /**
     * @var string
     */
    private $class;

    /**
     * @var AccessorInterface
     */
    private $accessor;

    /**
     * @param string            $class
     * @param AccessorInterface $accessor
     */
    public function __construct(string $class, AccessorInterface $accessor)
    {
        $this->class = $class;
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

        $existEmbObjects = $this->accessor->getValue($object);

        $relatedObjects = new ArrayCollection();
        foreach ($value as $i => $subValue) {
            $subPath = $path.'['.$i.']';

            if (!is_array($subValue)) {
                throw DeserializerRuntimeException::createInvalidDataType($subPath, gettype($subValue), 'array');
            }

            $relatedObject = $this->getRelatedObjectOrClass($existEmbObjects[$i] ?? null);

            $relatedObjects[$i] = $denormalizer->denormalize($relatedObject, $subValue, $context, $subPath);
        }

        $this->accessor->setValue($object, $relatedObjects);
    }

    /**
     * @param object|null $existEmbObject
     *
     * @return string
     */
    private function getRelatedObjectOrClass($existEmbObject)
    {
        if (null === $existEmbObject) {
            return $this->class;
        }

        $this->resolveProxy($existEmbObject);

        return $existEmbObject;
    }

    private function resolveProxy($relatedObject)
    {
        if (null !== $relatedObject && $relatedObject instanceof Proxy && !$relatedObject->__isInitialized()) {
            $relatedObject->__load();
        }
    }
}
