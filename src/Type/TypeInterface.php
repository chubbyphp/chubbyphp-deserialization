<?php

declare(strict_types=1);

namespace Chubbyphp\Deserialization\Type;

interface TypeInterface
{
    /**
     * @return string
     */
    public function getType(): string;

    const TYPE_BOOL = 'boolean';
    const TYPE_INT = 'integer';
    const TYPE_FLOAT = 'double';
    const TYPE_STRING = 'string';

    /**
     * @param mixed $value
     *
     * @return mixed
     */
    public function convert($value);
}
