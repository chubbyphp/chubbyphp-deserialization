<?php

declare(strict_types=1);

namespace Chubbyphp\Deserialization\Decoder;

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
     * @throws DecoderException
     */
    public function decode(string $data): array
    {
        $rawData = [];
        parse_str($data, $rawData);

        if ('' !== $data && [] === $rawData) {
            throw DecoderException::createNotParsable($this->getContentType());
        }

        return $this->cleanRawData($rawData);
    }

    /**
     * @param array $rawData
     *
     * @return array
     */
    private function cleanRawData(array $rawData): array
    {
        $data = [];
        foreach ($rawData as $rawKey => $value) {
            $key = (string) (int) $rawKey === $rawData ? (int) $rawKey : $rawKey;

            if (is_array($value)) {
                $data[$key] = $this->cleanRawData($value);
            } else {
                if (is_numeric($value)) {
                    if ((string) (int) $value === $value) {
                        $value = (int) $value;
                    } else {
                        $value = (float) $value;
                    }
                } elseif ('' === $value) {
                    $value = null;
                }

                $data[$key] = $value;
            }
        }

        return $data;
    }
}
