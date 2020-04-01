<?php

declare(strict_types=1);

namespace Chubbyphp\Deserialization\Policy;

use Chubbyphp\Deserialization\Denormalizer\DenormalizerContextInterface;

final class NullPolicy implements PolicyInterface
{
    /**
     * @deprecated
     *
     * @param object|mixed $object
     */
    public function isCompliant(DenormalizerContextInterface $context, $object): bool
    {
        @trigger_error('Use "isCompliantIncludingPath()" instead of "isCompliant()"', E_USER_DEPRECATED);

        return true;
    }

    public function isCompliantIncludingPath(string $path, object $object, DenormalizerContextInterface $context): bool
    {
        return true;
    }
}
