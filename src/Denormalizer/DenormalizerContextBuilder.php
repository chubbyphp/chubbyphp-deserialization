<?php

declare(strict_types=1);

namespace Chubbyphp\Deserialization\Denormalizer;

use Psr\Http\Message\ServerRequestInterface;

final class DenormalizerContextBuilder
{
    private ?ServerRequestInterface $request = null;

    /**
     * @var array<mixed>
     */
    private array $attributes = [];

    /**
     * @var array<int, string>|null
     */
    private ?array $allowedAdditionalFields = null;

    private bool $clearMissing = false;

    private function __construct()
    {
    }

    public static function create(): self
    {
        return new self();
    }

    public function setRequest(?ServerRequestInterface $request = null): self
    {
        $this->request = $request;

        return $this;
    }

    /**
     * @param array<mixed> $attributes
     */
    public function setAttributes(array $attributes): self
    {
        $this->attributes = $attributes;

        return $this;
    }

    /**
     * @param array<int, string>|null $allowedAdditionalFields
     */
    public function setAllowedAdditionalFields(?array $allowedAdditionalFields = null): self
    {
        $this->allowedAdditionalFields = $allowedAdditionalFields;

        return $this;
    }

    public function setClearMissing(bool $clearMissing): self
    {
        $this->clearMissing = $clearMissing;

        return $this;
    }

    public function getContext(): DenormalizerContextInterface
    {
        return new DenormalizerContext(
            $this->request,
            $this->attributes,
            $this->allowedAdditionalFields,
            $this->clearMissing
        );
    }
}
