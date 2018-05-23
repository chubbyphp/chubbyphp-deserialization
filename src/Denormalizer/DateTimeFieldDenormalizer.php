<?php

declare(strict_types=1);

namespace Chubbyphp\Deserialization\Denormalizer;

use Chubbyphp\Deserialization\DeserializerRuntimeException;

final class DateTimeFieldDenormalizer implements FieldDenormalizerInterface
{
    /**
     * @var FieldDenormalizerInterface
     */
    private $fieldDenormalizer;

    /**
     * @param FieldDenormalizerInterface $fieldDenormalizer
     */
    public function __construct(FieldDenormalizerInterface $fieldDenormalizer)
    {
        $this->fieldDenormalizer = $fieldDenormalizer;
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
        if (!is_string($value) || '' === $trimmedValue = trim($value)) {
            $this->fieldDenormalizer->denormalizeField($path, $object, $value, $context, $denormalizer);

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

        $this->fieldDenormalizer->denormalizeField($path, $object, $value, $context, $denormalizer);
    }
}
