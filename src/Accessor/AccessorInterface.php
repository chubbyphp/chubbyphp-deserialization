<?php

declare(strict_types=1);

namespace Chubbyphp\Deserialization\Accessor;

use Chubbyphp\Deserialization\DeserializerLogicException;

interface AccessorInterface
{
    /**
     * @param object $object
     * @param mixed  $value
     *
     * @throws DeserializerLogicException
     */
    public function setValue($object, $value);

    /**
     * @param object $object
     *
     * @return mixed
     *
     * @throws DeserializerLogicException
     */
    public function getValue($object);
}
