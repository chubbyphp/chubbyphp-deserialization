<?php

declare(strict_types=1);

namespace Chubbyphp\Deserialization\Denormalizer;

use Chubbyphp\Deserialization\Accessor\AccessorInterface;
use Chubbyphp\Deserialization\DeserializerRuntimeException;

final class DateTimeFieldDenormalizer implements FieldDenormalizerInterface
{
    /**
     * @deprecated
     *
     * @var FieldDenormalizerInterface
     */
    private $fieldDenormalizer;

    /**
     * @var AccessorInterface
     */
    private $accessor;

    /**
     * @var bool
     */
    private $emptyToNull;

    /**
     * @var \DateTimeZone|null
     */
    private $dateTimeZone;

    /**
     * @param AccessorInterface|FieldDenormalizerInterface $accessor
     */
    public function __construct($accessor, bool $emptyToNull = false, ?\DateTimeZone $dateTimeZone = null)
    {
        $this->emptyToNull = $emptyToNull;
        $this->dateTimeZone = $dateTimeZone;

        if ($accessor instanceof AccessorInterface) {
            $this->accessor = $accessor;

            return;
        }

        if ($accessor instanceof FieldDenormalizerInterface) {
            $this->setFieldDenormalizer($accessor);

            return;
        }

        throw new \TypeError(
            sprintf(
                '%s::__construct() expects parameter 1 to be %s|%s, %s given',
                self::class,
                AccessorInterface::class,
                FieldDenormalizerInterface::class,
                is_object($accessor) ? get_class($accessor) : gettype($accessor)
            )
        );
    }

    /**
     * @param object $object
     * @param mixed  $value
     *
     * @throws DeserializerRuntimeException
     */
    public function denormalizeField(
        string $path,
        $object,
        $value,
        DenormalizerContextInterface $context,
        ?DenormalizerInterface $denormalizer = null
    ): void {
        if ('' === $value && $this->emptyToNull) {
            $this->setValue($path, $object, null, $context, $denormalizer);

            return;
        }

        if (!is_string($value) || '' === $trimmedValue = trim($value)) {
            $this->setValue($path, $object, $value, $context, $denormalizer);

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

        $this->setValue($path, $object, $value, $context, $denormalizer);
    }

    private function setFieldDenormalizer(FieldDenormalizerInterface $fieldDenormalizer): void
    {
        @trigger_error(
            sprintf(
                'Use "%s" instead of "%s" as __construct argument',
                AccessorInterface::class,
                FieldDenormalizerInterface::class
            ),
            E_USER_DEPRECATED
        );

        $this->fieldDenormalizer = $fieldDenormalizer;
    }

    /**
     * @param object $object
     * @param mixed  $value
     */
    private function setValue(
        string $path,
        $object,
        $value,
        DenormalizerContextInterface $context,
        ?DenormalizerInterface $denormalizer = null
    ): void {
        if (null !== $this->accessor) {
            $this->accessor->setValue($object, $value);

            return;
        }

        $this->fieldDenormalizer->denormalizeField($path, $object, $value, $context, $denormalizer);
    }
}
