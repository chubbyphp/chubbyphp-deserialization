<?php

declare(strict_types=1);

namespace Chubbyphp\Deserialization\Decoder;

use Chubbyphp\Deserialization\DeserializerRuntimeException;

final class UrlEncodedTypeDecoder implements TypeDecoderInterface
{
    /**
     * @return string
     */
    public function getContentType(): string
    {
        return 'application/x-www-form-urlencoded';
    }

    /**
     * @param string $data
     *
     * @return array
     *
     * @throws DeserializerRuntimeException
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
     * @param array $rawData
     *
     * @return array
     */
    private function fixValues(array $rawData): array
    {
        $data = [];
        foreach ($rawData as $rawKey => $value) {
            $key = (string) (int) $rawKey === $rawKey ? (int) $rawKey : $rawKey;

            if (is_array($value)) {
                $data[$key] = $this->fixValues($value);
            } else {
                $data[$key] = $this->fixValue($value);
            }
        }

        return $data;
    }

    /**
     * @param string $value
     *
     * @return mixed
     */
    private function fixValue(string $value)
    {
        if ('' === $value) {
            return null;
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
