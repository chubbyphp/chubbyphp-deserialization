<?php

declare(strict_types=1);

namespace Chubbyphp\Deserialize\Mapping;

interface PropertyMappingInterface
{
    /**
     * @return string
     */
    public function getName(): string;

    /**
     * @return callable|null
     */
    public function getCallback();
}
