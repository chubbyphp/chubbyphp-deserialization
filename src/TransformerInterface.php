<?php

declare(strict_types=1);

namespace Chubbyphp\Deserialization;

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
     * @throws \InvalidArgumentException
     */
    public function transform(string $string, string $contentType): array;
}
