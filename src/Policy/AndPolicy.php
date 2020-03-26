<?php

declare(strict_types=1);

namespace Chubbyphp\Deserialization\Policy;

use Chubbyphp\Deserialization\Denormalizer\DenormalizerContextInterface;

final class AndPolicy implements PolicyInterface
{
    /**
     * @var array<int, PolicyInterface>
     */
    private $policies;

    /**
     * @param array<int, PolicyInterface> $policies
     */
    public function __construct(array $policies)
    {
        $this->policies = $policies;
    }

    public function isCompliant(DenormalizerContextInterface $context, object $object): bool
    {
        foreach ($this->policies as $policy) {
            if (false === $policy->isCompliant($context, $object)) {
                return false;
            }
        }

        return true;
    }

    public function isCompliantIncludingPath(object $object, DenormalizerContextInterface $context, string $path): bool
    {
        foreach ($this->policies as $policy) {
            if (method_exists($policy, 'isCompliantIncludingPath')) {
                if (false === $policy->isCompliantIncludingPath($object, $context, $path)) {
                    return false;
                }

                continue;
            }

            if (false === $policy->isCompliant($context,$object)) {
                return false;
            }
        }

        return true;
    }
}
