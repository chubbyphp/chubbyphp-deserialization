<?php

declare(strict_types=1);

namespace Chubbyphp\Deserialization\Denormalizer;

use Psr\Http\Message\ServerRequestInterface;

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

    /**
     * @return ServerRequestInterface|null
     */
    public function getRequest();
}
