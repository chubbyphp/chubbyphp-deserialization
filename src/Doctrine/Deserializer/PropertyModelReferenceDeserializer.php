<?php

declare(strict_types=1);

namespace Chubbyphp\DeserializationDoctrine\Deserializer;

use Chubbyphp\Deserialization\Deserializer\PropertyDeserializerInterface;
use Chubbyphp\Deserialization\DeserializerInterface;
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\Common\Persistence\Proxy;

final class PropertyModelReferenceDeserializer implements PropertyDeserializerInterface
{
    /**
     * @var ManagerRegistry
     */
    private $managerRegistry;

    /**
     * @var string
     */
    private $referenceClass;

    /**
     * @param ManagerRegistry $managerRegistry
     * @param string          $referenceClass
     */
    public function __construct(ManagerRegistry $managerRegistry, string $referenceClass)
    {
        $this->managerRegistry = $managerRegistry;
        $this->referenceClass = $referenceClass;
    }

    /**
     * @param string                $path
     * @param array|string|null     $serializedValue
     * @param object|null           $oldEntity
     * @param object                $object
     * @param DeserializerInterface $deserializer
     *
     * @return object|null
     */
    public function deserializeProperty(
        string $path,
        $serializedValue,
        $oldEntity = null,
        $object = null,
        DeserializerInterface $deserializer = null
    ) {
        $this->deserializerOrException($deserializer);

        $newEntity = null;

        if (is_array($serializedValue)) {
            if (null !== $oldEntity) {
                if ($oldEntity instanceof Proxy) {
                    $oldEntity->__load();
                }

                $newEntity = $deserializer->deserializeByObject($serializedValue, $oldEntity, $path);
            } else {
                $newEntity = $deserializer->deserializeByClass($serializedValue, $this->referenceClass, $path);
            }
        }

        if (is_string($serializedValue)) {
            $manager = $this->managerRegistry->getManagerForClass($this->referenceClass);
            $managerRegistry = $manager->getRepository($this->referenceClass);

            $newEntity = $managerRegistry->find($serializedValue);
        }

        return $newEntity;
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
