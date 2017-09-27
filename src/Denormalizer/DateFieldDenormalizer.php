<?php

declare(strict_types=1);

namespace Chubbyphp\Deserialization\Denormalizer;

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
     * @param string                            $path
     * @param object                            $object
     * @param mixed                             $value
     * @param DenormalizerInterface|null        $denormalizer
     * @param DenormalizerContextInterface|null $context
     */
    public function denormalizeField(
        string $path,
        $object,
        $value,
        DenormalizerInterface $denormalizer = null,
        DenormalizerContextInterface $context = null
    ) {
        try {
            $value = new \DateTime($value);
        } catch (\Exception $exception) {
        }

        $this->fieldDenormalizer->denormalizeField($path, $object, $value, $denormalizer, $context);
    }

    /**
     * @return mixed
     */
    public function getDefault()
    {
        return $this->fieldDenormalizer->getDefault();
    }
}
