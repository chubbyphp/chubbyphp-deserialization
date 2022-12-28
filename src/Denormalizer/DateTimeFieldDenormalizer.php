<?php

declare(strict_types=1);

namespace Chubbyphp\Deserialization\Denormalizer;

use Chubbyphp\Deserialization\Accessor\AccessorInterface;
use Chubbyphp\Deserialization\DeserializerRuntimeException;

/**
 * @deprecated Use {@link DateTimeImmutableFieldDenormalizer} instead
 */
final class DateTimeFieldDenormalizer implements FieldDenormalizerInterface
{
    public function __construct(
        private AccessorInterface $accessor,
        private bool $emptyToNull = false,
        private ?\DateTimeZone $dateTimeZone = null
    ) {
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
        @trigger_error(
            sprintf(
                '%s:denormalizeField use %s:denormalizeField',
                self::class,
                DateTimeImmutableFieldDenormalizer::class
            ),
            E_USER_DEPRECATED
        );

        if ('' === $value && $this->emptyToNull) {
            $this->accessor->setValue($object, null);

            return;
        }

        if (!\is_string($value) || '' === $trimmedValue = trim($value)) {
            $this->accessor->setValue($object, $value);

            return;
        }

        try {
            $dateTime = new \DateTime($trimmedValue);

            if (null !== $this->dateTimeZone) {
                $dateTime->setTimezone($this->dateTimeZone);
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
