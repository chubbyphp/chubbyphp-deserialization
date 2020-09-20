<?php

declare(strict_types=1);

namespace Chubbyphp\Deserialization\Policy;

use Chubbyphp\Deserialization\Denormalizer\DenormalizerContextInterface;

interface PolicyInterface
{
    public function isCompliant(string $path, object $object, DenormalizerContextInterface $context): bool;
}
