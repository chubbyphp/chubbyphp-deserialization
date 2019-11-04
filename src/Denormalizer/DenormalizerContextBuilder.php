<?php

declare(strict_types=1);

namespace Chubbyphp\Deserialization\Denormalizer;

use Psr\Http\Message\ServerRequestInterface;

final class DenormalizerContextBuilder implements DenormalizerContextBuilderInterface
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
    private $resetMissingFields = false;

    /**
     * @var array
     */
    private $attributes = [];

    private function __construct()
    {
    }

    public static function create(): DenormalizerContextBuilderInterface
    {
        return new self();
    }

    public function setAllowedAdditionalFields(
        array $allowedAdditionalFields = null
    ): DenormalizerContextBuilderInterface {
        $this->allowedAdditionalFields = $allowedAdditionalFields;

        return $this;
    }

    /**
     * @deprecated
     *
     * @param string[] $groups
     */
    public function setGroups(array $groups): DenormalizerContextBuilderInterface
    {
        $this->groups = $groups;

        return $this;
    }

    public function setRequest(ServerRequestInterface $request = null): DenormalizerContextBuilderInterface
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
            $this->attributes
        );
    }
}
