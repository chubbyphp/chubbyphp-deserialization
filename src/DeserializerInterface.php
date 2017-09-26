<?php

declare(strict_types=1);

namespace Chubbyphp\Deserialization;

use Chubbyphp\Deserialization\Denormalizer\DenormalizerContextInterface;

interface DeserializerInterface
{
    /**
     * @param object|string                $object
     * @param string                       $data
     * @param string                       $contentType
     * @param DenormalizerContextInterface $context
     *
     * @return object
     */
    public function deserialize(
        $object,
        string $data,
        string $contentType,
        DenormalizerContextInterface $context = null
    );
}
