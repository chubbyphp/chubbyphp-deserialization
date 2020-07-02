<?php

declare(strict_types=1);

namespace Chubbyphp\Deserialization\Policy;

use Chubbyphp\Deserialization\Denormalizer\DenormalizerContextInterface;

final class NotPolicy implements PolicyInterface
{
    /**
     * @var PolicyInterface
     */
    private $policy;

    public function __construct(PolicyInterface $policy)
    {
        $this->policy = $policy;
    }

    public function isCompliantIncludingPath(string $path, object $object, DenormalizerContextInterface $context): bool
    {
        return !$this->policy->isCompliantIncludingPath($path, $object, $context);
    }
}
