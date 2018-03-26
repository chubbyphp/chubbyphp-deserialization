<?php

declare(strict_types=1);

namespace Chubbyphp\Deserialization\Denormalizer\Relation\Doctrine;

use Chubbyphp\Deserialization\Accessor\AccessorInterface;
use Chubbyphp\Deserialization\Denormalizer\DenormalizerContextInterface;
use Chubbyphp\Deserialization\Denormalizer\DenormalizerInterface;
use Chubbyphp\Deserialization\Denormalizer\FieldDenormalizerInterface;
use Chubbyphp\Deserialization\DeserializerLogicException;
use Chubbyphp\Deserialization\DeserializerRuntimeException;
use Doctrine\Common\Persistence\Proxy;

final class EmbedOneFieldDenormalizer implements FieldDenormalizerInterface
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
    public function __construct($class, AccessorInterface $accessor)
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

        $embObject = $this->getEmbObjectOrClass($this->accessor->getValue($object));

        $this->accessor->setValue($object, $denormalizer->denormalize($embObject, $value, $context, $path));
    }

    /**
     * @param object|null $existEmbObject
     *
     * @return string
     */
    private function getEmbObjectOrClass($existEmbObject)
    {
        if (null === $existEmbObject) {
            return $this->class;
        }

        $this->resolveProxy($existEmbObject);

        return $existEmbObject;
    }

    private function resolveProxy($refObject)
    {
        if (null !== $refObject && $refObject instanceof Proxy && !$refObject->__isInitialized()) {
            $refObject->__load();
        }
    }
}
