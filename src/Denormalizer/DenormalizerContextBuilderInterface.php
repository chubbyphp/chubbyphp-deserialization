<?php

declare(strict_types=1);

namespace Chubbyphp\Deserialization\Denormalizer;

use Psr\Http\Message\ServerRequestInterface;

/**
 * @method setAttributes(array $attributes): self
 */
interface DenormalizerContextBuilderInterface
{
    /**
     * @return self
     */
    public static function create(): self;

    /**
     * @param array|null $allowedAdditionalFields
     *
     * @return self
     */
    public function setAllowedAdditionalFields(array $allowedAdditionalFields = null): self;

    /**
     * @deprecated
     *
     * @param string[] $groups
     *
     * @return self
     */
    public function setGroups(array $groups): self;

    /**
     * @param ServerRequestInterface|null $request
     *
     * @return self
     */
    public function setRequest(ServerRequestInterface $request = null): self;

    /**
     * @param array $attributes
     *
     * @return self
     */
    //public function setAttributes(array $attributes): self;

    /**
     * @return DenormalizerContextInterface
     */
    public function getContext(): DenormalizerContextInterface;
}
