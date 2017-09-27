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
        $class = get_class($object);

        try {
            $reflectionProperty = new \ReflectionProperty($class, $this->property);
        } catch (\ReflectionException $e) {
            throw AccessorException::createMissingProperty($class, $this->property);
        }

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
        $class = get_class($object);

        try {
            $reflectionProperty = new \ReflectionProperty($class, $this->property);
        } catch (\ReflectionException $e) {
            throw AccessorException::createMissingProperty($class, $this->property);
        }

        $reflectionProperty->setAccessible(true);

        return $reflectionProperty->getValue($object);
    }
}
