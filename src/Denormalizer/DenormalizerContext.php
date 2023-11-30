<?php

declare(strict_types=1);

namespace Chubbyphp\Deserialization\Denormalizer;

use Psr\Http\Message\ServerRequestInterface;

final class DenormalizerContext implements DenormalizerContextInterface
{
    /**
     * @param null|array<int, string> $allowedAdditionalFields
     * @param array<mixed>            $attributes
     */
    public function __construct(
        private ?ServerRequestInterface $request = null,
        private array $attributes = [],
        private ?array $allowedAdditionalFields = null,
        private bool $clearMissing = false
    ) {}

    public function getRequest(): ?ServerRequestInterface
    {
        return $this->request;
    }

    /**
     * @return array<mixed>
     */
    public function getAttributes(): array
    {
        return $this->attributes;
    }

    /**
     * @return mixed
     */
    public function getAttribute(string $name, mixed $default = null)
    {
        if (isset($this->attributes[$name])) {
            return $this->attributes[$name];
        }

        return $default;
    }

    /**
     * @param array<string, mixed> $attributes
     */
    public function withAttributes(array $attributes): DenormalizerContextInterface
    {
        $context = clone $this;
        $context->attributes = $attributes;

        return $context;
    }

    public function withAttribute(string $name, mixed $value): DenormalizerContextInterface
    {
        $context = clone $this;
        $context->attributes[$name] = $value;

        return $context;
    }

    /**
     * @return null|array<int, string>
     */
    public function getAllowedAdditionalFields(): ?array
    {
        return $this->allowedAdditionalFields;
    }

    public function isClearMissing(): bool
    {
        return $this->clearMissing;
    }
}
