<?php

declare(strict_types=1);

namespace Chubbyphp\Deserialization\Policy;

use Chubbyphp\Deserialization\Denormalizer\DenormalizerContextInterface;

/**
 * @method bool isCompliantIncludingPath(object $object, DenormalizerContextInterface $context, string $path)
 */
interface PolicyInterface
{
    /**
     * @deprecated
     */
    public function isCompliant(DenormalizerContextInterface $context, object $object): bool;

    //public function isCompliantIncludingPath(object $object, DenormalizerContextInterface $context, string $path): bool;
}
