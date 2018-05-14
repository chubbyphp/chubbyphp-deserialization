<?php

declare(strict_types=1);

namespace Chubbyphp\Deserialization\Type;

final class IntType implements TypeInterface
{
    /**
     * @return string
     */
    public function getType(): string
    {
        return self::TYPE_INT;
    }

    /**
     * @param mixed $value
     *
     * @return mixed
     */
    public function convert($value)
    {
        if (null === $value || is_int($value)) {
            return $value;
        }

        if (is_string($value)) {
            return $this->convertString($value);
        }

        if (is_float($value)) {
            return $this->convertFloat($value);
        }

        return $value;
    }

    /**
     * @param string $value
     *
     * @return int|string
     */
    private function convertString(string $value)
    {
        if ($value === (string) (int) $value) {
            return (int) $value;
        }

        return $value;
    }

    /**
     * @param float $value
     *
     * @return int|float
     */
    private function convertFloat(float $value)
    {
        if ((string) $value === (string) (int) $value) {
            return (int) $value;
        }

        return $value;
    }
}
