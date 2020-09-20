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
