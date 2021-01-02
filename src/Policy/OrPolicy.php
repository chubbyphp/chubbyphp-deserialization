<?php

declare(strict_types=1);

namespace Chubbyphp\Deserialization\Policy;

use Chubbyphp\Deserialization\Denormalizer\DenormalizerContextInterface;

final class OrPolicy implements PolicyInterface
{
    /**
     * @var array<int, PolicyInterface>
     */
    private array $policies;

    /**
     * @param array<int, PolicyInterface> $policies
     */
    public function __construct(array $policies)
    {
        $this->policies = $policies;
    }

    public function isCompliant(string $path, object $object, DenormalizerContextInterface $context): bool
    {
        foreach ($this->policies as $policy) {
            if ($policy->isCompliant($path, $object, $context)) {
                return true;
            }
        }

        return false;
    }
}
