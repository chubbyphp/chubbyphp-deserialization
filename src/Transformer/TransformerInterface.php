<?php

declare(strict_types=1);

namespace Chubbyphp\Deserialization\Transformer;

interface TransformerInterface
{
    /**
     * @param string $string
     *
     * @return array
     */
    public function transform(string $string): array;
}
