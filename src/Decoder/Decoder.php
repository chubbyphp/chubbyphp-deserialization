<?php

declare(strict_types=1);

namespace Chubbyphp\Deserialization\Decoder;

use Chubbyphp\DecodeEncode\Decoder\Decoder as BaseDecoder;
use Chubbyphp\DecodeEncode\Decoder\DecoderInterface as BaseDecoderInterface;
use Chubbyphp\DecodeEncode\LogicException;
use Chubbyphp\DecodeEncode\RuntimeException;
use Chubbyphp\Deserialization\DeserializerLogicException;
use Chubbyphp\Deserialization\DeserializerRuntimeException;

/**
 * @deprecated use \Chubbyphp\DecodeEncode\Decoder\Decoder
 */
final class Decoder implements DecoderInterface
{
    private BaseDecoderInterface $decoder;

    /**
     * @param array<int, TypeDecoderInterface> $decoderTypes
     */
    public function __construct(array $decoderTypes)
    {
        $this->decoder = new BaseDecoder($decoderTypes);
    }

    /**
     * @return array<int, string>
     */
    public function getContentTypes(): array
    {
        @trigger_error(
            sprintf(
                '%s:getContentTypes use %s:getContentTypes',
                self::class,
                BaseDecoder::class
            ),
            E_USER_DEPRECATED
        );

        return $this->decoder->getContentTypes();
    }

    /**
     * @throws DeserializerLogicException
     * @throws DeserializerRuntimeException
     *
     * @return array<string, null|array|bool|float|int|string>
     */
    public function decode(string $data, string $contentType): array
    {
        @trigger_error(
            sprintf(
                '%s:decode use %s:decode',
                self::class,
                BaseDecoder::class
            ),
            E_USER_DEPRECATED
        );

        try {
            return $this->decoder->decode($data, $contentType);
        } catch (RuntimeException $e) {
            throw new DeserializerRuntimeException($e->getMessage(), $e->getCode(), $e);
        } catch (LogicException $e) {
            throw new DeserializerLogicException($e->getMessage(), $e->getCode(), $e);
        }
    }
}
