<?php

declare(strict_types=1);

namespace Chubbyphp\Deserialization\Denormalizer;

use Psr\Http\Message\ServerRequestInterface;

final class DenormalizerContextBuilder implements DenormalizerContextBuilderInterface
{
    /**
     * @var array<int, string>|null
     */
    private $allowedAdditionalFields;

    /**
     * @deprecated
     *
     * @var array<int, string>
     */
    private $groups = [];

    /**
     * @var ServerRequestInterface|null
     */
    private $request;

    /**
     * @deprecated
     *
     * @var bool
     */
    private $resetMissingFields = false;

    /**
     * @var bool
     */
    private $clearMissing = false;

    /**
     * @var array<mixed>
     */
    private $attributes = [];

    private function __construct()
    {
    }

    public static function create(): DenormalizerContextBuilderInterface
    {
        return new self();
    }

    /**
     * @param array<int, string>|null $allowedAdditionalFields
     */
    public function setAllowedAdditionalFields(
        ?array $allowedAdditionalFields = null
    ): DenormalizerContextBuilderInterface {
        $this->allowedAdditionalFields = $allowedAdditionalFields;

        return $this;
    }

    /**
     * @deprecated
     *
     * @param array<int, string> $groups
     */
    public function setGroups(array $groups): DenormalizerContextBuilderInterface
    {
        $this->groups = $groups;

        return $this;
    }

    public function setRequest(?ServerRequestInterface $request = null): DenormalizerContextBuilderInterface
    {
        $this->request = $request;

        return $this;
    }

    /**
     * @deprecated
     */
    public function setResetMissingFields(bool $resetMissingFields): DenormalizerContextBuilderInterface
    {
        @trigger_error(
            'setResetMissingFields is broken by design, please do this your self by model or repository',
            E_USER_DEPRECATED
        );

        $this->resetMissingFields = $resetMissingFields;

        return $this;
    }

    public function setClearMissing(bool $clearMissing): DenormalizerContextBuilderInterface
    {
        $this->clearMissing = $clearMissing;

        return $this;
    }

    /**
     * @param array<mixed> $attributes
     */
    public function setAttributes(array $attributes): DenormalizerContextBuilderInterface
    {
        $this->attributes = $attributes;

        return $this;
    }

    public function getContext(): DenormalizerContextInterface
    {
        return new DenormalizerContext(
            $this->allowedAdditionalFields,
            $this->groups,
            $this->request,
            $this->resetMissingFields,
            $this->attributes,
            $this->clearMissing
        );
    }
}
