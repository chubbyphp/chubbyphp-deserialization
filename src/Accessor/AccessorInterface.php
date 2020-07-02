<?php

declare(strict_types=1);

namespace Chubbyphp\Deserialization\Accessor;

use Chubbyphp\Deserialization\DeserializerLogicException;

interface AccessorInterface
{
    /**
     * @param mixed $value
     *
     * @throws DeserializerLogicException
     */
    public function setValue(object $object, $value): void;

    /**
     * @throws DeserializerLogicException
     *
     * @return mixed
     */
    public function getValue(object $object);
}
