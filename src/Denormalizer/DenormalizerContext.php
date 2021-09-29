<?php

declare(strict_types=1);

namespace Chubbyphp\Deserialization\Denormalizer;

use Psr\Http\Message\ServerRequestInterface;

final class DenormalizerContext implements DenormalizerContextInterface
{
    private ?ServerRequestInterface $request;

    /**
     * @var array<mixed>
     */
    private array $attributes;

    /**
     * @var null|array<int, string>
     */
    private ?array $allowedAdditionalFields;

    private bool $clearMissing;

    /**
     * @param null|array<int, string> $allowedAdditionalFields
     * @param array<mixed>            $attributes
     */
    public function __construct(
        ?ServerRequestInterface $request = null,
        array $attributes = [],
        ?array $allowedAdditionalFields = null,
        bool $clearMissing = false
    ) {
        $this->request = $request;
        $this->attributes = $attributes;
        $this->allowedAdditionalFields = $allowedAdditionalFields;
        $this->clearMissing = $clearMissing;
    }

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
     * @param mixed $default
     *
     * @return mixed
     */
    public function getAttribute(string $name, $default = null)
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

    /**
     * @param mixed $value
     */
    public function withAttribute(string $name, $value): DenormalizerContextInterface
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
