<?php

declare(strict_types=1);

namespace Chubbyphp\Deserialization\Decoder;

use Chubbyphp\DecodeEncode\Decoder\JsonxTypeDecoder as BaseJsonxTypeDecoder;
use Chubbyphp\DecodeEncode\RuntimeException;
use Chubbyphp\Deserialization\DeserializerRuntimeException;

/**
 * @deprecated use \Chubbyphp\DecodeEncode\Decoder\JsonxTypeDecoder
 */
final class JsonxTypeDecoder implements TypeDecoderInterface
{
    private BaseJsonxTypeDecoder $jsonxTypeDecoder;

    public function __construct()
    {
        $this->jsonxTypeDecoder = new BaseJsonxTypeDecoder();
    }

    public function getContentType(): string
    {
        @trigger_error(
            sprintf(
                '%s:getContentType use %s:getContentType',
                self::class,
                BaseJsonxTypeDecoder::class
            ),
            E_USER_DEPRECATED
        );

        return $this->jsonxTypeDecoder->getContentType();
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
                BaseJsonxTypeDecoder::class
            ),
            E_USER_DEPRECATED
        );

        try {
            return $this->jsonxTypeDecoder->decode($data);
        } catch (RuntimeException $e) {
            throw new DeserializerRuntimeException($e->getMessage(), $e->getCode(), $e);
        }
    }
}
