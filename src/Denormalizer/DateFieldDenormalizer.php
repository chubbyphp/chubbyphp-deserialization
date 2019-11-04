<?php

declare(strict_types=1);

namespace Chubbyphp\Deserialization\Denormalizer;

use Chubbyphp\Deserialization\DeserializerRuntimeException;

/**
 * @deprecated use Chubbyphp\Deserialization\Denormalizer\DateTimeFieldDenormalizer
 */
final class DateFieldDenormalizer implements FieldDenormalizerInterface
{
    /**
     * @var DateTimeFieldDenormalizer
     */
    private $fieldDenormalizer;

    public function __construct(FieldDenormalizerInterface $fieldDenormalizer)
    {
        $this->fieldDenormalizer = new DateTimeFieldDenormalizer($fieldDenormalizer);
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
        DenormalizerInterface $denormalizer = null
    ): void {
        @trigger_error(sprintf('Use %s instead', DateTimeFieldDenormalizer::class), E_USER_DEPRECATED);

        $this->fieldDenormalizer->denormalizeField($path, $object, $value, $context, $denormalizer);
    }
}
