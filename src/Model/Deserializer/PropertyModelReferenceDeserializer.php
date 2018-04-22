<?php

declare(strict_types=1);

namespace Chubbyphp\DeserializationModel\Deserializer;

use Chubbyphp\Deserialization\Deserializer\PropertyDeserializerInterface;
use Chubbyphp\Deserialization\DeserializerInterface;
use Chubbyphp\Model\Reference\ModelReferenceInterface;
use Chubbyphp\Model\ResolverInterface;

final class PropertyModelReferenceDeserializer implements PropertyDeserializerInterface
{
    /**
     * @var ResolverInterface
     */
    private $resolver;

    /**
     * @var string
     */
    private $referenceClass;

    /**
     * @param ResolverInterface $resolver
     * @param string            $referenceClass
     */
    public function __construct(ResolverInterface $resolver, string $referenceClass)
    {
        $this->resolver = $resolver;
        $this->referenceClass = $referenceClass;
    }

    /**
     * @param string                  $path
     * @param array|string|null       $serializedValue
     * @param ModelReferenceInterface $reference
     * @param object                  $object
     * @param DeserializerInterface   $deserializer
     *
     * @return ModelReferenceInterface
     */
    public function deserializeProperty(
        string $path,
        $serializedValue,
        $reference = null,
        $object = null,
        DeserializerInterface $deserializer = null
    ) {
        $this->modelReferenceOrException($reference);
        $this->deserializerOrException($deserializer);

        $model = null;

        if (is_array($serializedValue)) {
            if (null !== $model = $reference->getModel()) {
                $model = $deserializer->deserializeByObject($serializedValue, $model, $path);
            } else {
                $model = $deserializer->deserializeByClass($serializedValue, $this->referenceClass, $path);
            }
        }

        if (is_string($serializedValue)) {
            $model = $this->resolver->find($this->referenceClass, $serializedValue);
        }

        $reference->setModel($model);

        return $reference;
    }

    /**
     * @param ModelReferenceInterface|null $reference
     *
     * @throws \RuntimeException
     */
    private function modelReferenceOrException($reference)
    {
        if (!$reference instanceof ModelReferenceInterface) {
            throw new \RuntimeException(
                sprintf(
                    'Object needs to implement: %s, given: %s',
                    ModelReferenceInterface::class,
                    is_object($reference) ? get_class($reference) : gettype($reference)
                )
            );
        }
    }

    /**
     * @param DeserializerInterface|null $deserializer
     *
     * @throws \RuntimeException
     */
    private function deserializerOrException(DeserializerInterface $deserializer = null)
    {
        if (null === $deserializer) {
            throw new \RuntimeException(sprintf('Deserializer needed: %s', DeserializerInterface::class));
        }
    }
}
