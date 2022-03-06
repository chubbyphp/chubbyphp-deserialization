<?php

declare(strict_types=1);

namespace Chubbyphp\Deserialization\Decoder;

use Chubbyphp\Deserialization\DeserializerRuntimeException;

final class UrlEncodedTypeDecoder implements TypeDecoderInterface
{
    public function getContentType(): string
    {
        return 'application/x-www-form-urlencoded';
    }

    /**
     * @throws DeserializerRuntimeException
     *
     * @return array<string, null|array|bool|float|int|string>
     */
    public function decode(string $data): array
    {
        $rawData = [];
        parse_str($data, $rawData);

        if ('' !== $data && [] === $rawData) {
            throw DeserializerRuntimeException::createNotParsable($this->getContentType());
        }

        return $this->fixValues($rawData);
    }

    /**
     * @param array<int|string, null|array|bool|float|int|string> $rawData
     *
     * @return array<int|string, null|array|bool|float|int|string>
     */
    private function fixValues(array $rawData): array
    {
        $data = [];
        foreach ($rawData as $key => $value) {
            $data[$key] = \is_array($value) ? $this->fixValues($value) : $this->fixValue($value);
        }

        return $data;
    }

    /**
     * @return null|bool|float|int|string
     */
    private function fixValue(string $value)
    {
        if ('' === $value) {
            return;
        }

        if ('true' === $value) {
            return true;
        }

        if ('false' === $value) {
            return false;
        }

        if (is_numeric($value) && '0' !== $value[0]) {
            if ((string) (int) $value === $value) {
                return (int) $value;
            }

            return (float) $value;
        }

        return $value;
    }
}
