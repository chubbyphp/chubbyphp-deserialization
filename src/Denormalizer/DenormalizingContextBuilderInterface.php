<?php

declare(strict_types=1);

namespace Chubbyphp\Deserialization\Denormalizer;

interface DenormalizingContextBuilderInterface
{
    /**
     * @return DenormalizingContextBuilderInterface
     */
    public static function create(): DenormalizingContextBuilderInterface;

    /**
     * @param bool $allowedAdditionalFields
     *
     * @return self
     */
    public function setAllowedAdditionalFields(bool $allowedAdditionalFields): self;

    /**
     * @param string[] $groups
     *
     * @return self
     */
    public function setGroups(array $groups): self;

    /**
     * @return DenormalizerContextInterface
     */
    public function getContext(): DenormalizerContextInterface;
}
