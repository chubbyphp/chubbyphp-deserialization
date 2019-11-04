<?php

declare(strict_types=1);

namespace Chubbyphp\Deserialization\Accessor;

use Chubbyphp\Deserialization\DeserializerLogicException;

final class MethodAccessor implements AccessorInterface
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
     *
     * @throws DeserializerLogicException
     */
    public function setValue($object, $value)
    {
        $set = 'set'.ucfirst($this->property);
        if (!method_exists($object, $set)) {
            throw DeserializerLogicException::createMissingMethod(get_class($object), [$set]);
        }

        return $object->{$set}($value);
    }

    /**
     * @param object $object
     *
     * @throws DeserializerLogicException
     *
     * @return mixed
     */
    public function getValue($object)
    {
        $get = 'get'.ucfirst($this->property);
        $has = 'has'.ucfirst($this->property);
        $is = 'is'.ucfirst($this->property);

        if (method_exists($object, $get)) {
            return $object->{$get}();
        }

        if (method_exists($object, $has)) {
            return $object->{$has}();
        }

        if (method_exists($object, $is)) {
            return $object->{$is}();
        }

        throw DeserializerLogicException::createMissingMethod(get_class($object), [$get, $has, $is]);
    }
}
