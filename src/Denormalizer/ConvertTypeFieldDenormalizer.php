<?php

declare(strict_types=1);

namespace Chubbyphp\Deserialization\Denormalizer;

use Chubbyphp\Deserialization\Accessor\AccessorInterface;
use Chubbyphp\Deserialization\DeserializerLogicException;
use Chubbyphp\Deserialization\DeserializerRuntimeException;

final class ConvertTypeFieldDenormalizer implements FieldDenormalizerInterface
{
    public const TYPE_BOOL = 'boolean';
    public const TYPE_FLOAT = 'float';
    public const TYPE_INT = 'int';
    public const TYPE_STRING = 'string';

    public const TYPES = [
        self::TYPE_BOOL,
        self::TYPE_FLOAT,
        self::TYPE_INT,
        self::TYPE_STRING,
    ];

    private AccessorInterface $accessor;

    private string $type;

    private bool $emptyToNull;

    /**
     * @throws DeserializerLogicException
     */
    public function __construct(AccessorInterface $accessor, string $type, bool $emptyToNull = false)
    {
        if (!\in_array($type, self::TYPES, true)) {
            throw DeserializerLogicException::createConvertTypeDoesNotExists($type);
        }

        $this->accessor = $accessor;
        $this->type = $type;
        $this->emptyToNull = $emptyToNull;
    }

    /**
     * @param mixed $value
     *
     * @throws DeserializerRuntimeException
     */
    public function denormalizeField(
        string $path,
        object $object,
        $value,
        DenormalizerContextInterface $context,
        ?DenormalizerInterface $denormalizer = null
    ): void {
        if ('' === $value && $this->emptyToNull) {
            $this->accessor->setValue($object, null);

            return;
        }

        $this->accessor->setValue($object, $this->convertType($value));
    }

    /**
     * @param mixed $value
     *
     * @return mixed
     */
    private function convertType($value)
    {
        $type = \gettype($value);

        if ($this->type === $type || !is_scalar($value)) {
            return $value;
        }

        switch ($this->type) {
            case self::TYPE_BOOL:
                return $this->convertBool($value);

            case self::TYPE_INT:
                return $this->convertInt($value);

            case self::TYPE_FLOAT:
                return $this->convertFloat($value);

            default:
                return $this->convertString($value);
        }
    }

    /**
     * @param bool|float|int|string $value
     *
     * @return bool|float|int|string
     */
    private function convertBool($value)
    {
        if ('true' === $value) {
            return true;
        }

        if ('false' === $value) {
            return false;
        }

        return $value;
    }

    /**
     * @param mixed $value
     *
     * @return bool|float|int|string
     */
    private function convertFloat($value)
    {
        if (!is_numeric($value)) {
            return $value;
        }

        return (float) $value;
    }

    /**
     * @param mixed $value
     *
     * @return bool|float|int|string
     */
    private function convertInt($value)
    {
        if (!is_numeric($value)) {
            return $value;
        }

        if ((string) (int) $value !== (string) $value) {
            return $value;
        }

        return (int) $value;
    }

    /**
     * @param bool|float|int|string $value
     *
     * @return bool|string
     */
    private function convertString($value)
    {
        if (\is_bool($value)) {
            return $value;
        }

        return (string) $value;
    }
}
