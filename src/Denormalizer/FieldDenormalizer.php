<?php

declare(strict_types=1);

namespace Chubbyphp\Deserialization\Denormalizer;

use Chubbyphp\Deserialization\Accessor\AccessorInterface;

final class FieldDenormalizer implements FieldDenormalizerInterface
{
    public function __construct(private AccessorInterface $accessor, private bool $emptyToNull = false) {}

    public function denormalizeField(
        string $path,
        object $object,
        mixed $value,
        DenormalizerContextInterface $context,
        ?DenormalizerInterface $denormalizer = null
    ): void {
        if ('' === $value && $this->emptyToNull) {
            $this->accessor->setValue($object, null);

            return;
        }

        $this->accessor->setValue($object, $value);
    }
}
