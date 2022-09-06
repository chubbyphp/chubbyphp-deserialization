<?php

declare(strict_types=1);

namespace Chubbyphp\Deserialization\Decoder;

use Chubbyphp\DecodeEncode\Decoder\JsonTypeDecoder as BaseJsonTypeDecoder;
use Chubbyphp\DecodeEncode\RuntimeException;
use Chubbyphp\Deserialization\DeserializerRuntimeException;

/**
 * @deprecated use \Chubbyphp\DecodeEncode\Decoder\JsonTypeDecoder
 */
final class JsonTypeDecoder implements TypeDecoderInterface
{
    private BaseJsonTypeDecoder $jsonTypeDecoder;

    public function __construct()
    {
        $this->jsonTypeDecoder = new BaseJsonTypeDecoder();
    }

    public function getContentType(): string
    {
        @trigger_error(
            sprintf(
                '%s:getContentType use %s:getContentType',
                self::class,
                BaseJsonTypeDecoder::class
            ),
            E_USER_DEPRECATED
        );

        return $this->jsonTypeDecoder->getContentType();
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
                BaseJsonTypeDecoder::class
            ),
            E_USER_DEPRECATED
        );

        try {
            return $this->jsonTypeDecoder->decode($data);
        } catch (RuntimeException $e) {
            throw new DeserializerRuntimeException($e->getMessage(), $e->getCode(), $e);
        }
    }
}
