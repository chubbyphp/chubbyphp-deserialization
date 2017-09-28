<?php

declare(strict_types=1);

namespace Chubbyphp\Deserialization\Denormalizer;

final class DenormalizingContextBuilder implements DenormalizingContextBuilderInterface
{
    /**
     * @var bool
     */
    private $allowedAdditionalFields;

    /**
     * @var string[]
     */
    private $groups;

    private function __construct()
    {
    }

    /**
     * @return DenormalizingContextBuilderInterface
     */
    public static function create(): DenormalizingContextBuilderInterface
    {
        $self = new self();
        $self->allowedAdditionalFields = false;
        $self->groups = [];

        return $self;
    }

    /**
     * @param bool $allowedAdditionalFields
     *
     * @return DenormalizingContextBuilderInterface
     */
    public function setAllowedAdditionalFields(bool $allowedAdditionalFields): DenormalizingContextBuilderInterface
    {
        $this->allowedAdditionalFields = $allowedAdditionalFields;

        return $this;
    }

    /**
     * @param string[] $groups
     *
     * @return DenormalizingContextBuilderInterface
     */
    public function setGroups(array $groups): DenormalizingContextBuilderInterface
    {
        $this->groups = $groups;

        return $this;
    }

    /**
     * @return DenormalizerContextInterface
     */
    public function getContext(): DenormalizerContextInterface
    {
        return new DenormalizerContext($this->allowedAdditionalFields, $this->groups);
    }
}
