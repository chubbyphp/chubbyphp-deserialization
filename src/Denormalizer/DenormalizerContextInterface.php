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
     * @param mixed $default
     *
     * @return mixed
     */
    public function getAttribute(string $name, $default = null);

    /**
     * @param array<string, mixed> $attributes
     */
    public function withAttributes(array $attributes): self;

    /**
     * @param mixed $value
     */
    public function withAttribute(string $name, $value): self;

    /**
     * @return null|array<int, string>
     */
    public function getAllowedAdditionalFields(): ?array;

    public function isClearMissing(): bool;
}
