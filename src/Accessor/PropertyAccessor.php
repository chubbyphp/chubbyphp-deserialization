<?php

declare(strict_types=1);

namespace Chubbyphp\Deserialization\Accessor;

use Chubbyphp\Deserialization\DeserializerLogicException;
use Doctrine\Persistence\Proxy;

final class PropertyAccessor implements AccessorInterface
{
    /**
     * @var string
     */
    private $property;

    public function __construct(string $property)
    {
        $this->property = $property;
    }

    /**
     * @param object $object
     * @param mixed  $value
     */
    public function setValue($object, $value): void
    {
        $class = $this->getClass($object);

        if (!property_exists($class, $this->property)) {
            throw DeserializerLogicException::createMissingProperty($class, $this->property);
        }

        $setter = \Closure::bind(
            function ($property, $value): void {
                $this->{$property} = $value;
            },
            $object,
            $class
        );

        $setter($this->property, $value);
    }

    /**
     * @param object $object
     *
     * @return mixed
     */
    public function getValue($object)
    {
        $class = $this->getClass($object);

        if (!property_exists($class, $this->property)) {
            throw DeserializerLogicException::createMissingProperty($class, $this->property);
        }

        $getter = \Closure::bind(
            function ($property) {
                return $this->{$property};
            },
            $object,
            $class
        );

        return $getter($this->property);
    }

    /**
     * @param object $object
     */
    private function getClass($object): string
    {
        if (interface_exists('Doctrine\Persistence\Proxy') && $object instanceof Proxy) {
            if (!$object->__isInitialized()) {
                $object->__load();
            }

            $reflectionParentClass = (new \ReflectionObject($object))->getParentClass();
            if ($reflectionParentClass instanceof \ReflectionClass) {
                return $reflectionParentClass->getName();
            }
        }

        return get_class($object);
    }
}
