<?php

declare(strict_types=1);

namespace Chubbyphp\Deserialization\Denormalizer;

use Chubbyphp\Deserialization\Accessor\AccessorInterface;
use Chubbyphp\Deserialization\DeserializerRuntimeException;

final class DateTimeFieldDenormalizer implements FieldDenormalizerInterface
{
    private AccessorInterface $accessor;

    private bool $emptyToNull;

    private ?\DateTimeZone $dateTimeZone;

    public function __construct(
        AccessorInterface $accessor,
        bool $emptyToNull = false,
        ?\DateTimeZone $dateTimeZone = null
    ) {
        $this->accessor = $accessor;
        $this->emptyToNull = $emptyToNull;
        $this->dateTimeZone = $dateTimeZone;
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
        if ('' === $value && $this->emptyToNull) {
            $this->accessor->setValue($object, null);

            return;
        }

        if (!is_string($value) || '' === $trimmedValue = trim($value)) {
            $this->accessor->setValue($object, $value);

            return;
        }

        try {
            $dateTime = new \DateTime($trimmedValue);

            if (null !== $this->dateTimeZone) {
                $dateTime->setTimezone($this->dateTimeZone);
            }

            $errors = \DateTime::getLastErrors();

            if (0 === $errors['warning_count'] && 0 === $errors['error_count']) {
                $value = $dateTime;
            }
        } catch (\Exception $exception) {
            error_clear_last();
        }

        $this->accessor->setValue($object, $value);
    }
}
