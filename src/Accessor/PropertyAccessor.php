<?php

declare(strict_types=1);

namespace Chubbyphp\Deserialization\Accessor;

use Chubbyphp\Deserialization\DeserializerLogicException;
use Doctrine\Common\Persistence\Proxy;

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
        if (interface_exists('Doctrine\Common\Persistence\Proxy') && $object instanceof Proxy) {
            $class = (new \ReflectionClass($object))->getParentClass()->name;
        } else {
            $class = get_class($object);
        }

        try {
            $reflectionProperty = new \ReflectionProperty($class, $this->property);
        } catch (\ReflectionException $e) {
            throw DeserializerLogicException::createMissingProperty($class, $this->property);
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
        if (interface_exists('Doctrine\Common\Persistence\Proxy') && $object instanceof Proxy) {
            $class = (new \ReflectionClass($object))->getParentClass()->name;
        } else {
            $class = get_class($object);
        }

        try {
            $reflectionProperty = new \ReflectionProperty($class, $this->property);
        } catch (\ReflectionException $e) {
            throw DeserializerLogicException::createMissingProperty($class, $this->property);
        }

        $reflectionProperty->setAccessible(true);

        return $reflectionProperty->getValue($object);
    }
}
