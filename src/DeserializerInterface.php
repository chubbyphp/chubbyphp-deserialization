<?php

declare(strict_types=1);

namespace Chubbyphp\Deserialize;

interface DeserializerInterface
{
    /**
     * @param array         $serializedData
     * @param object|string $object
     * @return object
     */
    public function deserializeFromArray(array $serializedData, $object);
}
