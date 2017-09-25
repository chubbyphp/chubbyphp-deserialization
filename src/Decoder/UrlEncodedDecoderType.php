<?php

declare(strict_types=1);

namespace Chubbyphp\Deserialization\Decoder;

final class UrlEncodedDecoderType implements DecoderTypeInterface
{
    /**
     * @var string
     */
    private $numericPrefix;

    /**
     * @var string
     */
    private $argSeperator;

    /**
     * @param string $numericPrefix
     * @param string $argSeperator
     */
    public function __construct(string $numericPrefix = '', string $argSeperator = '&')
    {
        $this->numericPrefix = $numericPrefix;
        $this->argSeperator = $argSeperator;
    }

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
        $data = str_replace($this->argSeperator, '&', $data);

        $rawData = [];
        parse_str($data, $rawData);

        if ('' !== $data && [] === $rawData) {
            throw DecoderException::createNotParsable($this->getContentType());
        }

        return $this->cleanRawData($rawData, strlen($this->numericPrefix));
    }

    /**
     * @param array $rawData
     * @param int   $numericPrefixLength
     *
     * @return array
     */
    private function cleanRawData(array $rawData, int $numericPrefixLength): array
    {
        $data = [];
        foreach ($rawData as $rawKey => $value) {
            if (0 !== $numericPrefixLength && 0 === strpos($rawKey, $this->numericPrefix)) {
                $rawSubKey = substr($rawKey, $numericPrefixLength);
                if (is_numeric($rawSubKey)) {
                    $rawKey = $rawSubKey;
                }
            }

            $key = is_numeric($rawKey) ? (int) $rawKey : $rawKey;

            if (is_array($value)) {
                $data[$key] = $this->cleanRawData($value, $numericPrefixLength);
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
