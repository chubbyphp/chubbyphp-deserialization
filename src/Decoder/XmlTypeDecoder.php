<?php

declare(strict_types=1);

namespace Chubbyphp\Deserialization\Decoder;

use Chubbyphp\DecodeEncode\Decoder\XmlTypeDecoder as BaseXmlTypeDecoder;
use Chubbyphp\DecodeEncode\RuntimeException;
use Chubbyphp\Deserialization\DeserializerRuntimeException;

/**
 * @deprecated use \Chubbyphp\DecodeEncode\Decoder\XmlTypeDecoder
 */
final class XmlTypeDecoder implements TypeDecoderInterface
{
    private BaseXmlTypeDecoder $xmlTypeDecoder;

    public function __construct()
    {
        $this->xmlTypeDecoder = new BaseXmlTypeDecoder();
    }

    public function getContentType(): string
    {
        @trigger_error(
            sprintf(
                '%s:getContentType use %s:getContentType',
                self::class,
                BaseXmlTypeDecoder::class
            ),
            E_USER_DEPRECATED
        );

        return $this->xmlTypeDecoder->getContentType();
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
                BaseXmlTypeDecoder::class
            ),
            E_USER_DEPRECATED
        );

        try {
            return $this->xmlTypeDecoder->decode($data);
        } catch (RuntimeException $e) {
            throw new DeserializerRuntimeException($e->getMessage(), $e->getCode(), $e);
        }
    }
}
