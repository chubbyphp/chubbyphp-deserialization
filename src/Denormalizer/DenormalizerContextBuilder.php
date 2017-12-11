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
     * @var string[]
     */
    private $groups;

    /**
     * @var ServerRequestInterface|null
     */
    private $request;

    private function __construct()
    {
    }

    /**
     * @return DenormalizerContextBuilderInterface
     */
    public static function create(): DenormalizerContextBuilderInterface
    {
        $self = new self();
        $self->allowedAdditionalFields = [];
        $self->groups = [];

        return $self;
    }

    /**
     * @param array|null $allowedAdditionalFields
     *
     * @return DenormalizerContextBuilderInterface
     */
    public function setAllowedAdditionalFields(?array $allowedAdditionalFields): DenormalizerContextBuilderInterface
    {
        $this->allowedAdditionalFields = $allowedAdditionalFields;

        return $this;
    }

    /**
     * @param string[] $groups
     *
     * @return DenormalizerContextBuilderInterface
     */
    public function setGroups(array $groups): DenormalizerContextBuilderInterface
    {
        $this->groups = $groups;

        return $this;
    }

    /**
     * @param ServerRequestInterface|null $request
     *
     * @return DenormalizerContextBuilderInterface
     */
    public function setRequest(ServerRequestInterface $request = null): DenormalizerContextBuilderInterface
    {
        $this->request = $request;

        return $this;
    }

    /**
     * @return DenormalizerContextInterface
     */
    public function getContext(): DenormalizerContextInterface
    {
        return new DenormalizerContext($this->allowedAdditionalFields, $this->groups, $this->request);
    }
}
