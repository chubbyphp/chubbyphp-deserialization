<?php

declare(strict_types=1);

namespace Chubbyphp\Deserialization\Decoder;

use Chubbyphp\Deserialization\DeserializerRuntimeException;

final class UrlEncodedDecoderType implements DecoderTypeInterface
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
            $key = (string) (int) $rawKey === $rawData ? (int) $rawKey : $rawKey;

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
        if (is_numeric($value)) {
            if ((string) (int) $value === $value) {
                return (int) $value;
            }

            return (float) $value;
        }

        if ('' === $value) {
            return null;
        }

        return $value;
    }
}
