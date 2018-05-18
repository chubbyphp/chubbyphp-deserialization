<?php

declare(strict_types=1);

namespace Chubbyphp\Deserialization\Denormalizer;

use Chubbyphp\Deserialization\Accessor\AccessorInterface;
use Chubbyphp\Deserialization\DeserializerLogicException;
use Chubbyphp\Deserialization\DeserializerRuntimeException;

final class ConvertTypeFieldDenormalizer implements FieldDenormalizerInterface
{
    /**
     * @var AccessorInterface
     */
    private $accessor;

    /**
     * @var string
     */
    private $type;

    const TYPE_INT = 'int';
    const TYPE_FLOAT = 'float';
    const TYPE_STRING = 'string';

    const TYPES = [
        self::TYPE_INT,
        self::TYPE_FLOAT,
        self::TYPE_STRING,
    ];

    /**
     * @param AccessorInterface $accessor
     * @param string            $type
     *
     * @throws DeserializerLogicException
     */
    public function __construct(AccessorInterface $accessor, string $type)
    {
        if (!in_array($type, self::TYPES, true)) {
            throw DeserializerLogicException::createConvertTypeDoesNotExists($type);
        }

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
        $this->accessor->setValue($object, $this->convertType($value));
    }

    /**
     * @param mixed $value
     *
     * @return mixed
     */
    private function convertType($value)
    {
        $type = gettype($value);

        if (!is_scalar($value) || $this->type === $type) {
            return $value;
        }

        $convertedValue = $value;

        settype($convertedValue, $this->type);

        if ((string) $value !== (string) $convertedValue) {
            return $value;
        }

        return $convertedValue;
    }
}
