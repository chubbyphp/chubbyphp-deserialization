<?php

declare(strict_types=1);

namespace Chubbyphp\Deserialization\Denormalizer;

use Psr\Http\Message\ServerRequestInterface;

final class DenormalizerContext implements DenormalizerContextInterface
{
    /**
     * @var array|null
     */
    private $allowedAdditionalFields;

    /**
     * @deprecated
     *
     * @var string[]
     */
    private $groups = [];

    /**
     * @var ServerRequestInterface|null
     */
    private $request;

    /**
     * @var bool
     */
    private $resetMissingFields;

    /**
     * @var array
     */
    private $attributes;

    /**
     * @param string[] $groups
     */
    public function __construct(
        array $allowedAdditionalFields = null,
        array $groups = [],
        ServerRequestInterface $request = null,
        bool $resetMissingFields = false,
        array $attributes = []
    ) {
        $this->allowedAdditionalFields = $allowedAdditionalFields;
        $this->groups = $groups;
        $this->request = $request;

        if ($resetMissingFields) {
            @trigger_error(
                'resetMissingFields is broken by design, please do this your self by model or repository',
                E_USER_DEPRECATED
            );
        }

        $this->resetMissingFields = $resetMissingFields;
        $this->attributes = $attributes;
    }

    /**
     * @return array|null
     */
    public function getAllowedAdditionalFields()
    {
        return $this->allowedAdditionalFields;
    }

    /**
     * @deprecated
     *
     * @return string[]
     */
    public function getGroups(): array
    {
        return $this->groups;
    }

    /**
     * @return ServerRequestInterface|null
     */
    public function getRequest()
    {
        return $this->request;
    }

    /**
     * @deprecated
     */
    public function isResetMissingFields(): bool
    {
        return $this->resetMissingFields;
    }

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
     * @param mixed $value
     */
    public function withAttribute(string $name, $value): DenormalizerContextInterface
    {
        $context = clone $this;
        $context->attributes[$name] = $value;

        return $context;
    }
}
