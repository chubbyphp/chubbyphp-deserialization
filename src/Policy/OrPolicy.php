<?php

declare(strict_types=1);

namespace Chubbyphp\Deserialization\Policy;

use Chubbyphp\Deserialization\Denormalizer\DenormalizerContextInterface;

final class OrPolicy implements PolicyInterface
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

    /**
     * @deprecated
     *
     * @param object|mixed $object
     */
    public function isCompliant(DenormalizerContextInterface $context, $object): bool
    {
        @trigger_error('Use "isCompliantIncludingPath()" instead of "isCompliant()"', E_USER_DEPRECATED);

        foreach ($this->policies as $policy) {
            if ($policy->isCompliant($context, $object)) {
                return true;
            }
        }

        return false;
    }

    public function isCompliantIncludingPath(string $path, object $object, DenormalizerContextInterface $context): bool
    {
        foreach ($this->policies as $policy) {
            if (method_exists($policy, 'isCompliantIncludingPath')) {
                if ($policy->isCompliantIncludingPath($path, $object, $context)) {
                    return true;
                }

                continue;
            }

            @trigger_error('Use "isCompliantIncludingPath()" instead of "isCompliant()"', E_USER_DEPRECATED);

            if ($policy->isCompliant($context, $object)) {
                return true;
            }
        }

        return false;
    }
}
