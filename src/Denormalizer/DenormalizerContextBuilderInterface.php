<?php

declare(strict_types=1);

namespace Chubbyphp\Deserialization\Denormalizer;

use Psr\Http\Message\ServerRequestInterface;

/**
 * @method DenormalizerContextBuilderInterface setResetMissingFields(bool $clearMissing)
 * @method DenormalizerContextBuilderInterface setClearMissing(bool $clearMissing)
 * @method DenormalizerContextBuilderInterface setAttributes(array $attributes)
 */
interface DenormalizerContextBuilderInterface
{
    public static function create(): self;

    /**
     * @param array<int, string>|null $allowedAdditionalFields
     */
    public function setAllowedAdditionalFields(?array $allowedAdditionalFields = null): self;

    /**
     * @deprecated
     *
     * @param array<int, string> $groups
     */
    public function setGroups(array $groups): self;

    public function setRequest(?ServerRequestInterface $request = null): self;

    //public function setClearMissing(bool $clearMissing): self;

    /**
     * @param array<mixed> $attributes
     */
    //public function setAttributes(array $attributes): self;

    public function getContext(): DenormalizerContextInterface;
}
