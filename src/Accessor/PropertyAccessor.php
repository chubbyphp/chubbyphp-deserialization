<?php

declare(strict_types=1);

namespace Chubbyphp\Deserialization\Accessor;

final class PropertyAccessor implements AccessorInterface
{
    /**
     * @var string
     */
    private $property;

    /**
     * @param string $property
     */
    public function __construct(string $property)
    {
        $this->property = $property;
    }

    /**
     * @param object $object
     * @param mixed  $value
     */
    public function setValue($object, $value)
    {
        $reflectionProperty = new \ReflectionProperty(get_class($object), $this->property);
        $reflectionProperty->setAccessible(true);

        $reflectionProperty->setValue($object, $value);
    }

    /**
     * @param object $object
     *
     * @return mixed
     */
    public function getValue($object)
    {
        $reflectionProperty = new \ReflectionProperty(get_class($object), $this->property);
        $reflectionProperty->setAccessible(true);

        return $reflectionProperty->getValue($object);
    }
}
