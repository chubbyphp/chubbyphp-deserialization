<?php

declare(strict_types=1);

namespace Chubbyphp\Deserialization\Denormalizer;

use Psr\Http\Message\ServerRequestInterface;

interface DenormalizerContextInterface
{
    /**
     * @return array|null
     */
    public function getAllowedAdditionalFields();

    /**
     * @return string[]
     */
    public function getGroups(): array;

    /**
     * @return ServerRequestInterface|null
     */
    public function getRequest();

    // /**
    //  * @return bool
    //  */
    // public function isResetMissingFields(): bool;
}
