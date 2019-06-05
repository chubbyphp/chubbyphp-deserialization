<?php

declare(strict_types=1);

namespace Chubbyphp\Deserialization\Policy;

use Chubbyphp\Deserialization\Denormalizer\DenormalizerContextInterface;

final class NullPolicy implements PolicyInterface
{
    /**
     * @param DenormalizerContextInterface $context
     * @param object|mixed                 $object
     *
     * @return bool
     */
    public function isCompliant(DenormalizerContextInterface $context, $object): bool
    {
        return true;
    }
}
