<?php

declare(strict_types=1);

namespace Chubbyphp\Deserialization\Denormalizer;

use Psr\Http\Message\ServerRequestInterface;

interface DenormalizerContextInterface
{
    public function getRequest(): ?ServerRequestInterface;

    /**
     * @return array<string, mixed>
     */
    public function getAttributes(): array;

    /**
     * @return mixed
     */
    public function getAttribute(string $name, mixed $default = null);

    /**
     * @param array<string, mixed> $attributes
     */
    public function withAttributes(array $attributes): self;

    public function withAttribute(string $name, mixed $value): self;

    /**
     * @return null|array<int, string>
     */
    public function getAllowedAdditionalFields(): ?array;

    public function isClearMissing(): bool;
}
