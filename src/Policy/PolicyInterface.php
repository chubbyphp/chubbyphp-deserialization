<?php

declare(strict_types=1);

namespace Chubbyphp\Deserialization\Policy;

use Chubbyphp\Deserialization\Denormalizer\DenormalizerContextInterface;

/**
 * @method bool isCompliantIncludingPath(string $path, object $object, DenormalizerContextInterface $context)
 */
interface PolicyInterface
{
    /**
     * @deprecated
     */
    public function isCompliant(DenormalizerContextInterface $context, object $object): bool;

    //public function isCompliantIncludingPath(string $path, object $object, DenormalizerContextInterface $context): bool;
}
