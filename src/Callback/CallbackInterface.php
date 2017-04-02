<?php

declare(strict_types=1);

namespace Chubbyphp\Deserialize\Callback;

use Chubbyphp\Deserialize\DeserializerInterface;

interface CallbackInterface
{
    /**
     * @param DeserializerInterface $deserializer
     * @param $serializedValue
     * @param $oldValue
     * @param $object
     * @return mixed
     */
    public function __invoke(DeserializerInterface $deserializer, $serializedValue, $oldValue, $object);
}
