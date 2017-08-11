<?php

declare(strict_types=1);

namespace Chubbyphp\Deserialization;

use Chubbyphp\Deserialization\Transformer\TransformerException;

interface TransformerInterface
{
    /**
     * @return string[]
     */
    public function getContentTypes(): array;

    /**
     * @param string $string
     * @param string $contentType
     *
     * @return array
     *
     * @throws TransformerException
     * @throws \InvalidArgumentException
     */
    public function transform(string $string, string $contentType): array;
}
