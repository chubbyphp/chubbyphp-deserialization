<?php

declare(strict_types=1);

namespace Chubbyphp\Deserialization\Policy;

use Chubbyphp\Deserialization\Denormalizer\DenormalizerContextInterface;

final class NullPolicy implements PolicyInterface
{
    public function isCompliantIncludingPath(string $path, object $object, DenormalizerContextInterface $context): bool
    {
        return true;
    }
}
