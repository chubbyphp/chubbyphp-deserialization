<?php

declare(strict_types=1);

namespace Chubbyphp\Deserialization\Policy;

use Chubbyphp\Deserialization\Denormalizer\DenormalizerContextInterface;

final class CallbackPolicy implements PolicyInterface
{
    /**
     * @var callable
     */
    private $callback;

    public function __construct(callable $callback)
    {
        $this->callback = $callback;
    }

    /**
     * @param object $object
     */
    public function isCompliant(DenormalizerContextInterface $context, $object): bool
    {
        return ($this->callback)($context, $object);
    }
}
