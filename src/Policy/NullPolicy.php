<?php

declare(strict_types=1);

namespace Chubbyphp\Deserialization\Policy;

use Chubbyphp\Deserialization\Denormalizer\DenormalizerContextInterface;

final class NullPolicy implements PolicyInterface
{
    /**
     * @param object $object
     */
    public function isCompliant(DenormalizerContextInterface $context, $object): bool
    {
        return true;
    }
}
