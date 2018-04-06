<?php

declare(strict_types=1);

namespace Chubbyphp\Deserialization\Denormalizer;

use Chubbyphp\Deserialization\DeserializerRuntimeException;

final class DateFieldDenormalizer implements FieldDenormalizerInterface
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
            $value = new \DateTime($trimmedValue);
        } catch (\Exception $exception) {
            error_clear_last();
        }

        $this->fieldDenormalizer->denormalizeField($path, $object, $value, $context, $denormalizer);
    }
}
