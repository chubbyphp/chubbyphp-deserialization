<?php

declare(strict_types=1);

namespace Chubbyphp\Deserialization\Denormalizer;

final class DenormalizerContext implements DenormalizerContextInterface
{
    /**
     * @var bool
     */
    private $allowAdditionalFields;

    /**
     * @var string[]
     */
    private $groups;

    /**
     * @param bool          $allowAdditionalFields
     * @param null|string[] $groups
     */
    public function __construct(bool $allowAdditionalFields = false, array $groups = [])
    {
        $this->allowAdditionalFields = $allowAdditionalFields;
        $this->groups = $groups;
    }

    /**
     * @return bool
     */
    public function isAllowedAdditionalFields(): bool
    {
        return $this->allowAdditionalFields;
    }

    /**
     * @return string[]
     */
    public function getGroups(): array
    {
        return $this->groups;
    }
}
