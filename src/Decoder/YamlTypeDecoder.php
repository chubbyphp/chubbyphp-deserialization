<?php

declare(strict_types=1);

namespace Chubbyphp\Deserialization\Decoder;

use Chubbyphp\DecodeEncode\Decoder\YamlTypeDecoder as BaseYamlTypeDecoder;
use Chubbyphp\DecodeEncode\RuntimeException;
use Chubbyphp\Deserialization\DeserializerRuntimeException;

/**
 * @deprecated use \Chubbyphp\DecodeEncode\Decoder\YamlTypeDecoder
 */
final class YamlTypeDecoder implements TypeDecoderInterface
{
    private BaseYamlTypeDecoder $yamlTypeDecoder;

    public function __construct()
    {
        $this->yamlTypeDecoder = new BaseYamlTypeDecoder();
    }

    public function getContentType(): string
    {
        @trigger_error(
            sprintf(
                '%s:getContentType use %s:getContentType',
                self::class,
                BaseYamlTypeDecoder::class
            ),
            E_USER_DEPRECATED
        );

        return $this->yamlTypeDecoder->getContentType();
    }

    /**
     * @return array<string, null|array|bool|float|int|string>
     *
     * @throws DeserializerRuntimeException
     */
    public function decode(string $data): array
    {
        @trigger_error(
            sprintf(
                '%s:decode use %s:decode',
                self::class,
                BaseYamlTypeDecoder::class
            ),
            E_USER_DEPRECATED
        );

        try {
            return $this->yamlTypeDecoder->decode($data);
        } catch (RuntimeException $e) {
            throw new DeserializerRuntimeException($e->getMessage(), $e->getCode(), $e);
        }
    }
}
