<?php

declare(strict_types=1);

namespace Chubbyphp\Deserialization\Denormalizer;

use Chubbyphp\Deserialization\Accessor\AccessorInterface;
use Chubbyphp\Deserialization\DeserializerRuntimeException;

final class FieldDenormalizer implements FieldDenormalizerInterface
{
    /**
     * @var AccessorInterface
     */
    private $accessor;

    /**
     * @var bool
     */
    private $emptyToNull;

    /**
     * @param AccessorInterface $accessor
     * @param bool              $emptyToNull
     */
    public function __construct(AccessorInterface $accessor, bool $emptyToNull = false)
    {
        $this->accessor = $accessor;
        $this->emptyToNull = $emptyToNull;
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
    ): void {
        if ('' === $value && $this->emptyToNull) {
            $this->accessor->setValue($object, null);

            return;
        }

        $this->accessor->setValue($object, $value);
    }
}
