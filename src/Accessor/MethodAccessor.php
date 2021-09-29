<?php

declare(strict_types=1);

namespace Chubbyphp\Deserialization\Accessor;

use Chubbyphp\Deserialization\DeserializerLogicException;

final class MethodAccessor implements AccessorInterface
{
    private string $property;

    public function __construct(string $property)
    {
        $this->property = $property;
    }

    /**
     * @param mixed $value
     *
     * @throws DeserializerLogicException
     */
    public function setValue(object $object, $value): void
    {
        $set = 'set'.ucfirst($this->property);
        if (!method_exists($object, $set)) {
            throw DeserializerLogicException::createMissingMethod(\get_class($object), [$set]);
        }

        $object->{$set}($value);
    }

    /**
     * @throws DeserializerLogicException
     *
     * @return mixed
     */
    public function getValue(object $object)
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

        throw DeserializerLogicException::createMissingMethod(\get_class($object), [$get, $has, $is]);
    }
}
