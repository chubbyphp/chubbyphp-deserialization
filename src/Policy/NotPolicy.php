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

    public function isCompliant(DenormalizerContextInterface $context, object $object): bool
    {
        return !$this->policy->isCompliant($context, $object);
    }

    public function isCompliantIncludingPath(object $object, DenormalizerContextInterface $context, string $path): bool
    {
        return !$this->policy->isCompliantIncludingPath($object, $context, $path);
    }
}
