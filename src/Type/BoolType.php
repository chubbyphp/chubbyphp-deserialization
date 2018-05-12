<?php

declare(strict_types=1);

namespace Chubbyphp\Deserialization\Type;

final class BoolType implements TypeInterface
{
    /**
     * @return string
     */
    public function getType(): string
    {
        return self::TYPE_BOOL;
    }

    /**
     * @param mixed $value
     *
     * @return mixed
     */
    public function convert($value)
    {
        if (null === $value || is_bool($value)) {
            return $value;
        }

        if (is_string($value)) {
            return $this->convertString($value);
        }

        if (is_int($value)) {
            return $this->convertInt($value);
        }

        return $value;
    }

    /**
     * @param string $value
     *
     * @return bool|string
     */
    private function convertString(string $value)
    {
        if ('true' === $value || '1' === $value) {
            return true;
        }

        if ('false' === $value || '0' === $value) {
            return false;
        }

        return $value;
    }

    /**
     * @param int $value
     *
     * @return bool|int
     */
    private function convertInt(int $value)
    {
        if (1 === $value) {
            return true;
        }

        if (0 === $value) {
            return false;
        }

        return $value;
    }
}
