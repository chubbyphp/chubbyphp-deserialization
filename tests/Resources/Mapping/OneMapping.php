<?php

declare(strict_types=1);

namespace Chubbyphp\Tests\Deserialize\Resources\Mapping;

use Chubbyphp\Deserialize\DeserializerInterface;
use Chubbyphp\Deserialize\Mapping\ObjectMappingInterface;
use Chubbyphp\Deserialize\Mapping\PropertyMapping;
use Chubbyphp\Deserialize\Mapping\PropertyMappingInterface;
use Chubbyphp\Tests\Deserialize\Resources\Model\Many;
use Chubbyphp\Tests\Deserialize\Resources\Model\One;

final class OneMapping implements ObjectMappingInterface
{
        /**
     * @return string
     */
    public function getClass(): string
    {
        return One::class;
    }

    /**
     * @return PropertyMappingInterface[]
     */
    public function getPropertyMappings(): array
    {
        return [
            new PropertyMapping('name'),
            new PropertyMapping('manies', function(DeserializerInterface $deserializer, $serializedValues, $oldValues, $object) {
                $newValues = [];
                foreach ($serializedValues as $i => $serializedValue) {
                    $serializedValue['one'] = $object;

                    if (isset($oldValues[$i])) {
                        $relatedObject = $oldValues[$i];

                        unset($oldValues[$i]);
                    } else {
                        $relatedObject = Many::class;
                    }

                    $newValues[$i] = $deserializer->deserializeFromArray($serializedValue, $relatedObject);
                }

                foreach ($oldValues as $oldValue) {
                    $deserializer->deserializeFromArray(['one' => null], $oldValue);
                }

                return $newValues;
            }),
        ];
    }
}
