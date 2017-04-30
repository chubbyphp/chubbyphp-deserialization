<?php

declare(strict_types=1);

namespace Chubbyphp\Deserialize\Mapping;

use Chubbyphp\Deserialize\Deserialize\PropertyDeserialize;
use Chubbyphp\Deserialize\Deserialize\PropertyDeserializeInterface;

final class PropertyMapping implements PropertyMappingInterface
{
    /**
     * @var string
     */
    private $name;

    /**
     * @var PropertyDeserializeInterface
     */
    private $propertyDeserializer;

    /**
     * @param string $name
     * @param PropertyDeserializeInterface|null $propertyDeserializer
     */
    public function __construct(string $name, PropertyDeserializeInterface $propertyDeserializer = null)
    {
        $this->name = $name;
        $this->propertyDeserializer = $propertyDeserializer ?? new PropertyDeserialize();
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return PropertyDeserializeInterface
     */
    public function getPropertyDeserializer(): PropertyDeserializeInterface
    {
        return $this->propertyDeserializer;
    }
}
