<?php

declare(strict_types=1);

namespace Chubbyphp\Deserialization\Policy;

use Chubbyphp\Deserialization\Denormalizer\DenormalizerContextInterface;

final class OrPolicy implements PolicyInterface
{
    /**
     * @var array|PolicyInterface[]
     */
    private $policies;

    /**
     * @param array|PolicyInterface[] $policies
     */
    public function __construct(array $policies)
    {
        $this->policies = $policies;
    }

    /**
     * @param object|mixed $object
     */
    public function isCompliant(DenormalizerContextInterface $context, $object): bool
    {
        foreach ($this->policies as $policy) {
            if ($policy->isCompliant($context, $object)) {
                return true;
            }
        }

        return false;
    }
}
