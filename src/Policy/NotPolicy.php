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

    /**
     * @param object $object
     */
    public function isCompliant(DenormalizerContextInterface $context, $object): bool
    {
        return !$this->policy->isCompliant($context, $object);
    }
}
