<?php

declare(strict_types=1);

namespace Chubbyphp\Deserialization\Denormalizer;

use Chubbyphp\Deserialization\Accessor\AccessorInterface;
use Chubbyphp\Deserialization\DeserializerRuntimeException;

final class DateTimeImmutableFieldDenormalizer implements FieldDenormalizerInterface
{
    public function __construct(
        private AccessorInterface $accessor,
        private bool $emptyToNull = false,
        private ?\DateTimeZone $dateTimeZone = null
    ) {}

    /**
     * @throws DeserializerRuntimeException
     */
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

        if (!\is_string($value) || '' === $trimmedValue = trim($value)) {
            $this->accessor->setValue($object, $value);

            return;
        }

        try {
            $dateTime = new \DateTimeImmutable($trimmedValue);

            if (null !== $this->dateTimeZone) {
                $dateTime = $dateTime->setTimezone($this->dateTimeZone);
            }

            $errors = \DateTimeImmutable::getLastErrors();

            if (false === $errors || 0 === $errors['warning_count'] && 0 === $errors['error_count']) {
                $value = $dateTime;
            }
        } catch (\Exception) {
            error_clear_last();
        }

        $this->accessor->setValue($object, $value);
    }
}
