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
     * @var string[]
     */
    private $groups = [];

    /**
     * @param bool     $allowedAdditionalFields
     * @param string[] $groups
     */
    public function __construct($allowedAdditionalFields = false, array $groups = [])
    {
        $this->allowedAdditionalFields = $allowedAdditionalFields;
        $this->groups = $groups;
    }

    /**
     * @return bool
     */
    public function isAllowedAdditionalFields(): bool
    {
        return $this->allowedAdditionalFields;
    }

    /**
     * @return string[]
     */
    public function getGroups(): array
    {
        return $this->groups;
    }
}
