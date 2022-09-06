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
     * @return mixed
     *
     * @throws DeserializerLogicException
     */
    public function getValue(object $object);
}
