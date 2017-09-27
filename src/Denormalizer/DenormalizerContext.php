<?php

declare(strict_types=1);

namespace Chubbyphp\Deserialization\Denormalizer;

final class DenormalizerContext implements DenormalizerContextInterface
{
    /**
     * @var bool
     */
    private $allowedAdditionalFields = false;

    /**
     * @var bool
     */
    private $replaceMode = false;

    /**
     * @var string[]
     */
    private $groups = [];

    /**
     * @param bool $allowedAdditionalFields
     *
     * @return self
     */
    public function setAllowedAdditionalFields(bool $allowedAdditionalFields): self
    {
        $this->allowedAdditionalFields = $allowedAdditionalFields;

        return $this;
    }

    /**
     * @return bool
     */
    public function isAllowedAdditionalFields(): bool
    {
        return $this->allowedAdditionalFields;
    }

    /**
     * @param bool $replaceMode
     *
     * @return self
     */
    public function setReplaceMode(bool $replaceMode): self
    {
        $this->replaceMode = $replaceMode;

        return $this;
    }

    /**
     * @return bool
     */
    public function isReplaceMode(): bool
    {
        return $this->replaceMode;
    }

    /**
     * @param string[] $groups
     *
     * @return self
     */
    public function setGroups(array $groups): self
    {
        $this->groups = $groups;

        return $this;
    }

    /**
     * @return string[]
     */
    public function getGroups(): array
    {
        return $this->groups;
    }
}
