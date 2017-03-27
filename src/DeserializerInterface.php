<?php

declare(strict_types=1);

namespace Chubbyphp\Deserialize;

interface DeserializerInterface
{
    /**
     * @param array $data
     * @param string $class
     * @return object
     */
    public function deserializeFromArray(array $data, string $class);
}
