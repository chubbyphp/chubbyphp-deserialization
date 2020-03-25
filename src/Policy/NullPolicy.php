<?php

declare(strict_types=1);

namespace Chubbyphp\Deserialization\Policy;

use Chubbyphp\Deserialization\Denormalizer\DenormalizerContextInterface;

final class NullPolicy implements PolicyInterface
{
    public function isCompliant(DenormalizerContextInterface $context, object $object): bool
    {
        return true;
    }

    public function isCompliantIncludingPath(object $object, DenormalizerContextInterface $context, string $path): bool
    {
        return true;
    }
}
