<?php

declare(strict_types=1);

namespace Chubbyphp\Deserialization\Accessor;

interface AccessorInterface
{
    /**
     * @param object $object
     * @param mixed  $value
     */
    public function setValue($object, $value);

    /**
     * @param object $object
     *
     * @return mixed
     */
    public function getValue($object);
}
