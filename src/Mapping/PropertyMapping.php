<?php

declare(strict_types=1);

namespace Chubbyphp\Deserialization\Mapping;

use Chubbyphp\Deserialization\Deserializer\PropertyDeserializer;
use Chubbyphp\Deserialization\Deserializer\PropertyDeserializerInterface;

final class PropertyMapping implements PropertyMappingInterface
{
    /**
     * @var string
     */
    private $name;

    /**
     * @var PropertyDeserializerInterface
     */
    private $propertyDeserializer;

    /**
     * @param string                             $name
     * @param PropertyDeserializerInterface|null $propertyDeserializer
     */
    public function __construct(string $name, PropertyDeserializerInterface $propertyDeserializer = null)
    {
        $this->name = $name;
        $this->propertyDeserializer = $propertyDeserializer ?? new PropertyDeserializer();
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return PropertyDeserializerInterface
     */
    public function getPropertyDeserializer(): PropertyDeserializerInterface
    {
        return $this->propertyDeserializer;
    }
}
