<?php

declare(strict_types=1);

namespace Chubbyphp\Deserialization\Denormalizer;

interface DenormalizerContextInterface
{
    /**
     * @return bool
     */
    public function isAllowedAdditionalFields(): bool;

    /**
     * @return string[]
     */
    public function getGroups(): array;
}
