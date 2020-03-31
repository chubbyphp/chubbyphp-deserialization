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
     * @deprecated
     */
    public function isCompliant(DenormalizerContextInterface $context, object $object): bool
    {
        @trigger_error('Use "isCompliantIncludingPath()" instead of "isCompliant()"', E_USER_DEPRECATED);

        return !$this->policy->isCompliant($context, $object);
    }

    public function isCompliantIncludingPath(string $path, object $object, DenormalizerContextInterface $context): bool
    {
        if (method_exists($this->policy, 'isCompliantIncludingPath')) {
            return !$this->policy->isCompliantIncludingPath($path, $object, $context);
        }

        @trigger_error('Use "isCompliantIncludingPath()" instead of "isCompliant()"', E_USER_DEPRECATED);

        return !$this->policy->isCompliant($context, $object);
    }
}
