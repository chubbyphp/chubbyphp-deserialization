<?php

declare(strict_types=1);

namespace Chubbyphp\Tests\Deserialize\Resources\Mapping;

use Chubbyphp\Deserialize\DeserializerInterface;
use Chubbyphp\Deserialize\Mapping\ObjectMappingInterface;
use Chubbyphp\Deserialize\Mapping\PropertyMapping;
use Chubbyphp\Deserialize\Mapping\PropertyMappingInterface;
use Chubbyphp\Tests\Deserialize\Resources\Model\Many;

final class ManyMapping implements ObjectMappingInterface
{
    /**
     * @return string
     */
    public function getClass(): string
    {
        return Many::class;
    }

    /**
     * @return PropertyMappingInterface[]
     */
    public function getPropertyMappings(): array
    {
        return [
            new PropertyMapping('name'),
            new PropertyMapping('one', function(DeserializerInterface $deserializer, $serializedValue, $oldValue, $object) {
                if (is_object($serializedValue)) {
                    return $serializedValue;
                }

                if (null === $serializedValue) {
                    return null;
                }

                if (null !== $oldValue) {
                    return $deserializer->deserializeByObject($serializedValue, $oldValue);
                }

                return $deserializer->deserializeByClass($serializedValue, Many::class);
            }),
        ];
    }
}
