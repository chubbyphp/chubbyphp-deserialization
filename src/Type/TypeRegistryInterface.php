<?php

declare(strict_types=1);

namespace Chubbyphp\Deserialization\Type;

interface TypeRegistryInterface
{
    /**
     * @param string $type
     * @param mixed  $value
     *
     * @return mixed
     *
     * @throws DeserializerLogicException
     */
    public function convert(string $type, $value);
}
