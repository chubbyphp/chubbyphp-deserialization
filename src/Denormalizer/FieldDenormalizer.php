<?php

declare(strict_types=1);

namespace Chubbyphp\Deserialization\Denormalizer;

use Chubbyphp\Deserialization\Accessor\AccessorInterface;

final class FieldDenormalizer implements FieldDenormalizerInterface
{
    /**
     * @var AccessorInterface
     */
    private $accessor;

    /**
     * @var mixed
     */
    private $default;

    public function __construct(AccessorInterface $accessor, $default = null)
    {
        $this->accessor = $accessor;
        $this->default = $default;
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
        $this->accessor->setValue($object, $value);
    }

    /**
     * @return mixed|null
     */
    public function getDefault()
    {
        return $this->default;
    }
}
