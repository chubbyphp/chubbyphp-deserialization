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
     * @deprecated
     */
    public function isCompliant(DenormalizerContextInterface $context, object $object): bool
    {
        @trigger_error('Use "isCompliantIncludingPath()" instead of "isCompliant()"', E_USER_DEPRECATED);

        return ($this->callback)($context, $object);
    }

    public function isCompliantIncludingPath(object $object, DenormalizerContextInterface $context, string $path): bool
    {
        return ($this->callback)($context, $object, $path);
    }
}
