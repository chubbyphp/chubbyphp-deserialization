<?php

declare(strict_types=1);

namespace Chubbyphp\Deserialization\Accessor;

use Chubbyphp\Deserialization\DeserializerLogicException;
use Doctrine\Persistence\Proxy;

final class PropertyAccessor implements AccessorInterface
{
    public function __construct(private string $property)
    {
    }

    /**
     * @param mixed $value
     */
    public function setValue(object $object, $value): void
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
     * @return mixed
     */
    public function getValue(object $object)
    {
        $class = $this->getClass($object);

        if (!property_exists($class, $this->property)) {
            throw DeserializerLogicException::createMissingProperty($class, $this->property);
        }

        $getter = \Closure::bind(
            fn ($property) => $this->{$property},
            $object,
            $class
        );

        return $getter($this->property);
    }

    private function getClass(object $object): string
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

        return $object::class;
    }
}
