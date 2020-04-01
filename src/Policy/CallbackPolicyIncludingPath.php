<?php

declare(strict_types=1);

namespace Chubbyphp\Deserialization\Policy;

use Chubbyphp\Deserialization\Denormalizer\DenormalizerContextInterface;
use Chubbyphp\Deserialization\DeserializerLogicException;

final class CallbackPolicyIncludingPath implements PolicyInterface
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
     *
     * @throws DeserializerLogicException
     */
    public function isCompliant(DenormalizerContextInterface $context, object $object): bool
    {
        throw DeserializerLogicException::createDeprecatedMethod(__CLASS__, ['isCompliant']);
    }

    public function isCompliantIncludingPath(string $path, object $object, DenormalizerContextInterface $context): bool
    {
        return ($this->callback)($path, $object, $context);
    }
}
