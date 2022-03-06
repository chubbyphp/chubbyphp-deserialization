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

    private string $type;

    /**
     * @throws DeserializerLogicException
     */
    public function __construct(private AccessorInterface $accessor, string $type, private bool $emptyToNull = false)
    {
        if (!\in_array($type, self::TYPES, true)) {
            throw DeserializerLogicException::createConvertTypeDoesNotExists($type);
        }
        $this->type = $type;
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

        return match ($this->type) {
            self::TYPE_BOOL => $this->convertBool($value),
            self::TYPE_INT => $this->convertInt($value),
            self::TYPE_FLOAT => $this->convertFloat($value),
            default => $this->convertString($value),
        };
    }

    private function convertBool(bool|float|int|string $value): bool|float|int|string
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
     */
    private function convertFloat($value): bool|float|string
    {
        if (!is_numeric($value)) {
            return $value;
        }

        return (float) $value;
    }

    /**
     * @param mixed $value
     */
    private function convertInt($value): bool|float|int|string
    {
        if (!is_numeric($value)) {
            return $value;
        }

        if ((string) (int) $value !== (string) $value) {
            return $value;
        }

        return (int) $value;
    }

    private function convertString(bool|float|int|string $value): bool|string
    {
        if (\is_bool($value)) {
            return $value;
        }

        return (string) $value;
    }
}
