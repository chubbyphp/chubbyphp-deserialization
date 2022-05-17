<?php

declare(strict_types=1);

namespace Chubbyphp\Deserialization\Decoder;

use Chubbyphp\DecodeEncode\Decoder\UrlEncodedTypeDecoder as BaseUrlEncodedTypeDecoder;
use Chubbyphp\DecodeEncode\RuntimeException;
use Chubbyphp\Deserialization\DeserializerRuntimeException;

/**
 * @deprecated use \Chubbyphp\DecodeEncode\Decoder\UrlEncodedTypeDecoder
 */
final class UrlEncodedTypeDecoder implements TypeDecoderInterface
{
    private BaseUrlEncodedTypeDecoder $urlEncodedTypeDecoder;

    public function __construct()
    {
        $this->urlEncodedTypeDecoder = new BaseUrlEncodedTypeDecoder();
    }

    public function getContentType(): string
    {
        @trigger_error(
            sprintf(
                '%s:getContentType use %s:getContentType',
                self::class,
                BaseUrlEncodedTypeDecoder::class
            ),
            E_USER_DEPRECATED
        );

        return $this->urlEncodedTypeDecoder->getContentType();
    }

    /**
     * @throws DeserializerRuntimeException
     *
     * @return array<string, null|array|bool|float|int|string>
     */
    public function decode(string $data): array
    {
        @trigger_error(
            sprintf(
                '%s:decode use %s:decode',
                self::class,
                BaseUrlEncodedTypeDecoder::class
            ),
            E_USER_DEPRECATED
        );

        try {
            return $this->urlEncodedTypeDecoder->decode($data);
        } catch (RuntimeException $e) {
            throw new DeserializerRuntimeException($e->getMessage(), $e->getCode(), $e);
        }
    }
}
