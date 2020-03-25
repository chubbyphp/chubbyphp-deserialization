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

    public function isCompliant(DenormalizerContextInterface $context, object $object): bool
    {
        foreach ($this->policies as $policy) {
            if ($policy->isCompliant($context, $object)) {
                return true;
            }
        }

        return false;
    }

    public function isCompliantIncludingPath(object $object, DenormalizerContextInterface $context, string  $path): bool
    {
        foreach ($this->policies as $policy) {
            if ($policy->isCompliantIncludingPath($object, $context, $path)) {
                return true;
            }
        }

        return false;
    }
}
