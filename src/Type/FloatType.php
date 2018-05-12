<?php

declare(strict_types=1);

namespace Chubbyphp\Deserialization\Type;

final class FloatType implements TypeInterface
{
    /**
     * @return string
     */
    public function getType(): string
    {
        return self::TYPE_FLOAT;
    }

    /**
     * @param mixed $value
     *
     * @return mixed
     */
    public function convert($value)
    {
        if (null === $value || is_float($value)) {
            return $value;
        }

        if (is_string($value)) {
            return $this->convertString($value);
        }

        if (is_int($value)) {
            return $this->convertInt($value);
        }

        if (is_bool($value)) {
            return $this->convertBool($value);
        }

        return $value;
    }

    /**
     * @param string $value
     *
     * @return float|string
     */
    private function convertString(string $value)
    {
        if ($value === (string) (float) $value) {
            return (float) $value;
        }

        return $value;
    }

    /**
     * @param int $value
     *
     * @return float
     */
    private function convertInt(int $value): float
    {
        return (float) $value;
    }

    /**
     * @param bool $value
     *
     * @return float
     */
    private function convertBool(bool $value): float
    {
        return $value ? 1.0 : 0.0;
    }
}
