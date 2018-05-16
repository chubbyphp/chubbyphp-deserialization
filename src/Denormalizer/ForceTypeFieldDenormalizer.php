<?php

declare(strict_types=1);

namespace Chubbyphp\Deserialization\Denormalizer;

use Chubbyphp\Deserialization\Accessor\AccessorInterface;
use Chubbyphp\Deserialization\DeserializerRuntimeException;

final class ForceTypeFieldDenormalizer implements FieldDenormalizerInterface
{
    /**
     * @var AccessorInterface
     */
    private $accessor;

    /**
     * @var string
     */
    private $type;

    const TYPE_BOOL = 'boolean';
    const TYPE_INT = 'integer';
    const TYPE_FLOAT = 'float';
    const TYPE_STRING = 'string';

    const TYPES = [
        self::TYPE_BOOL,
        self::TYPE_INT,
        self::TYPE_FLOAT,
        self::TYPE_STRING,
    ];

    /**
     * @param AccessorInterface $accessor
     * @param string            $type
     */
    public function __construct(AccessorInterface $accessor, string $type)
    {
        $this->accessor = $accessor;
        $this->type = $type;
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
        $this->accessor->setValue($object, $this->forceType($value));
    }

    /**
     * @param mixed$value
     *
     * @return mixed
     */
    private function forceType($value)
    {
        $type = gettype($value);

        if (!is_scalar($value) || $this->type === $type) {
            return $value;
        }

        if (!in_array($this->type, self::TYPES, true)) {
            return $value;
        }

        $forcedValue = $value;

        settype($forcedValue, $this->type);

        if ((string) $value !== (string) $forcedValue) {
            return $value;
        }

        return $forcedValue;
    }
}
