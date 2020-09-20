<?php

declare(strict_types=1);

namespace Chubbyphp\Deserialization\Denormalizer;

use Chubbyphp\Deserialization\DeserializerRuntimeException;

final class CallbackFieldDenormalizer implements FieldDenormalizerInterface
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
     * @param mixed $value
     *
     * @throws DeserializerRuntimeException
     */
    public function denormalizeField(
        string $path,
        object $object,
        $value,
        DenormalizerContextInterface $context,
        ?DenormalizerInterface $denormalizer = null
    ): void {
        $callback = $this->callback;

        $callback($path, $object, $value, $context, $denormalizer);
    }
}
