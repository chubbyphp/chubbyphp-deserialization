<?php

declare(strict_types=1);

namespace Chubbyphp\Deserialization\Type;

final class StringType implements TypeInterface
{
    /**
     * @return string
     */
    public function getType(): string
    {
        return self::TYPE_STRING;
    }

    /**
     * @param mixed $value
     *
     * @return mixed
     */
    public function convert($value)
    {
        if (null === $value || is_string($value)) {
            return $value;
        }

        if (is_int($value)) {
            return $this->convertInt($value);
        }

        if (is_float($value)) {
            return $this->convertFloat($value);
        }

        if (is_bool($value)) {
            return $this->convertBool($value);
        }

        return $value;
    }

    /**
     * @param int $value
     *
     * @return string
     */
    private function convertInt(int $value): string
    {
        return (string) $value;
    }

    /**
     * @param float $value
     *
     * @return string
     */
    private function convertFloat(float $value)
    {
        return (string) $value;
    }

    /**
     * @param bool $value
     *
     * @return string
     */
    private function convertBool(bool $value): string
    {
        return $value ? 'true' : 'false';
    }
}
