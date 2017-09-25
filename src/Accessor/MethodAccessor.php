<?php

declare(strict_types=1);

namespace Chubbyphp\Deserialization\Accessor;

final class MethodAccessor implements AccessorInterface
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
     *
     * @throws \RuntimeException
     */
    public function setValue($object, $value)
    {
        $set = 'set'.ucfirst($this->property);
        if (!method_exists($object, $set)) {
            throw new \RuntimeException(
                sprintf('Missing method to set property %s on class %s', $this->property, get_class($object))
            );
        }

        return $object->$set($value);
    }

    /**
     * @param object $object
     *
     * @return mixed
     *
     * @throws \RuntimeException
     */
    public function getValue($object)
    {
        $get = 'get'.ucfirst($this->property);
        $has = 'has'.ucfirst($this->property);
        $is = 'is'.ucfirst($this->property);

        if (method_exists($object, $get)) {
            return $object->$get();
        }

        if (method_exists($object, $has)) {
            return $object->$has();
        }

        if (method_exists($object, $is)) {
            return $object->$is();
        }

        throw new \RuntimeException(
            sprintf('Missing method to get property %s on class %ss', $this->property, get_class($object))
        );
    }
}
