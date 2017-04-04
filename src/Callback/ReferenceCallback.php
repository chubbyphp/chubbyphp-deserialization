<?php

declare(strict_types=1);

namespace Chubbyphp\Deserialize\Callback;

use Chubbyphp\Deserialize\DeserializerInterface;
use Chubbyphp\Model\Reference\ModelReferenceInterface;
use Chubbyphp\Model\ResolverInterface;

final class ReferenceCallback implements CallbackInterface
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
        $this->resolver       = $resolver;
        $this->referenceClass = $referenceClass;
    }

    /**
     * @param DeserializerInterface   $deserializer
     * @param array|string|null       $serializedValue
     * @param ModelReferenceInterface $reference
     * @param object $object
     * @return ModelReferenceInterface
     */
    public function __invoke(DeserializerInterface $deserializer, $serializedValue, $reference, $object)
    {
        $model = null;

        if (is_array($serializedValue)) {
            $model = $deserializer->deserializeByClass($serializedValue, $this->referenceClass);
        }

        if (is_string($serializedValue)) {
            $model = $this->resolver->find($this->referenceClass, $serializedValue);
        }

        $reference->setModel($model);

        return $reference;
    }
}
