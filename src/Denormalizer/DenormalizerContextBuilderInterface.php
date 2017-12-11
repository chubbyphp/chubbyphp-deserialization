<?php

declare(strict_types=1);

namespace Chubbyphp\Deserialization\Denormalizer;

use Psr\Http\Message\ServerRequestInterface;

interface DenormalizerContextBuilderInterface
{
    /**
     * @return self
     */
    public static function create(): DenormalizerContextBuilderInterface;

    /**
     * @param array|null $allowedAdditionalFields
     *
     * @return self
     */
    public function setAllowedAdditionalFields(?array $allowedAdditionalFields): self;

    /**
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
     * @return DenormalizerContextInterface
     */
    public function getContext(): DenormalizerContextInterface;
}
