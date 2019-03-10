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
     * @param AccessorInterface|FieldDenormalizerInterface $accessor
     */
    public function __construct($accessor, bool $emptyToNull = false)
    {
        $this->emptyToNull = $emptyToNull;

        if ($accessor instanceof AccessorInterface) {
            $this->accessor = $accessor;

            return;
        }

        if ($accessor instanceof FieldDenormalizerInterface) {
            @trigger_error(
                sprintf(
                    'Use "%s" instead of "%s" as __construct argument',
                    AccessorInterface::class,
                    FieldDenormalizerInterface::class
                ),
                E_USER_DEPRECATED
            );

            $this->fieldDenormalizer = $accessor;

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
     * @param string                       $path
     * @param object                       $object
     * @param mixed                        $value
     * @param DenormalizerContextInterface $context
     * @param DenormalizerInterface|null   $denormalizer
     *
     * @throws DeserializerRuntimeException
     */
    public function denormalizeField(
        string $path,
        $object,
        $value,
        DenormalizerContextInterface $context,
        DenormalizerInterface $denormalizer = null
    ) {
        if ($this->emptyToNull && '' === $value) {
            $this->setValue($path, $object, null, $context, $denormalizer);

            return;
        }

        if (!is_string($value) || '' === $trimmedValue = trim($value)) {
            $this->setValue($path, $object, $value, $context, $denormalizer);

            return;
        }

        try {
            $dateTime = new \DateTime($trimmedValue);
            $errors = \DateTime::getLastErrors();

            if (0 === $errors['warning_count'] && 0 === $errors['error_count']) {
                $value = $dateTime;
            }
        } catch (\Exception $exception) {
            error_clear_last();
        }

        $this->setValue($path, $object, $value, $context, $denormalizer);
    }

    /**
     * @param string                       $path
     * @param object                       $object
     * @param mixed                        $value
     * @param DenormalizerContextInterface $context
     * @param DenormalizerInterface|null   $denormalizer
     */
    private function setValue(
        string $path,
        $object,
        $value,
        DenormalizerContextInterface $context,
        DenormalizerInterface $denormalizer = null
    ) {
        if (null !== $this->accessor) {
            $this->accessor->setValue($object, $value);

            return;
        }

        $this->fieldDenormalizer->denormalizeField($path, $object, $value, $context, $denormalizer);
    }
}
