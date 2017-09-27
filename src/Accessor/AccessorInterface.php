<?php

declare(strict_types=1);

namespace Chubbyphp\Deserialization\Accessor;

interface AccessorInterface
{
    /**
     * @param object $object
     * @param mixed  $value
     *
     * @throws AccessorException
     */
    public function setValue($object, $value);

    /**
     * @param object $object
     *
     * @return mixed
     *
     * @throws AccessorException
     */
    public function getValue($object);
}
