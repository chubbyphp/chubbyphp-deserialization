<?php

declare(strict_types=1);

namespace Chubbyphp\Deserialization;

interface TransformerInterface
{
    /**
     * @param string $string
     * @param string $contentType
     *
     * @return array
     *
     * @throws \InvalidArgumentException
     */
    public function transform(string $string, string $contentType): array;
}
