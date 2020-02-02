<?php

declare(strict_types=1);

namespace Chubbyphp\Deserialization\Denormalizer;

use Psr\Http\Message\ServerRequestInterface;

/**
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

    /**
     * @param array $attributes
     *
     * @return self
     */
    //public function setAttributes(array $attributes): self;

    public function getContext(): DenormalizerContextInterface;
}
