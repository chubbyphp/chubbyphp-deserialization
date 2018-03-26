<?php

declare(strict_types=1);

namespace Chubbyphp\Deserialization\Doctrine\Accessor;

use Chubbyphp\Deserialization\Accessor\AccessorInterface;
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
        $reflectionProperty = $this->getReflectionProperty($this->getClass($object));
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
        $reflectionProperty = $this->getReflectionProperty($this->getClass($object));
        $reflectionProperty->setAccessible(true);

        return $reflectionProperty->getValue($object);
    }

    /**
     * @param object $object
     *
     * @return string
     */
    private function getClass($object): string
    {
        if ($object instanceof Proxy) {
            return (new \ReflectionClass($object))->getParentClass()->name;
        }

        return get_class($object);
    }

    /**
     * @param string $class
     *
     * @return \ReflectionProperty
     */
    private function getReflectionProperty(string $class): \ReflectionProperty
    {
        try {
            return new \ReflectionProperty($class, $this->property);
        } catch (\ReflectionException $e) {
            throw DeserializerLogicException::createMissingProperty($class, $this->property);
        }
    }
}
