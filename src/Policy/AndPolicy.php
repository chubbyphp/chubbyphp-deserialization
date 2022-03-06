<?php

declare(strict_types=1);

namespace Chubbyphp\Deserialization\Policy;

use Chubbyphp\Deserialization\Denormalizer\DenormalizerContextInterface;

final class AndPolicy implements PolicyInterface
{
    /**
     * @param array<int, PolicyInterface> $policies
     */
    public function __construct(private array $policies)
    {
    }

    public function isCompliant(string $path, object $object, DenormalizerContextInterface $context): bool
    {
        foreach ($this->policies as $policy) {
            if (!$policy->isCompliant($path, $object, $context)) {
                return false;
            }
        }

        return true;
    }
}
