<?php

declare(strict_types=1);

namespace Chubbyphp\Deserialization\Transformer;

final class UrlEncodedTransformer implements TransformerInterface
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
     * @param string $string
     *
     * @return array
     */
    public function transform(string $string): array
    {
        $rawData = [];
        parse_str(str_replace($this->argSeperator, '&', $string), $rawData);

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
                $data[$key] = $value;
            }
        }

        return $data;
    }
}
