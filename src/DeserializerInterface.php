<?php

declare(strict_types=1);

namespace Chubbyphp\Deserialization;

interface DeserializerInterface
{
    /**
     * @param array  $serializedData
     * @param string $class
     *
     * @return object
     */
    public function deserializeByClass(array $serializedData, string $class);

    /**
     * @param array  $serializedData
     * @param object $object
     *
     * @return object
     */
    public function deserializeByObject(array $serializedData, $object);
}
