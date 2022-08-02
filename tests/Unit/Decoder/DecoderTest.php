<?php

declare(strict_types=1);

namespace Chubbyphp\Tests\Deserialization\Unit\Decoder;

use Chubbyphp\DecodeEncode\Decoder\Decoder as BaseDecoder;
use Chubbyphp\Deserialization\Decoder\Decoder;
use Chubbyphp\Deserialization\Decoder\TypeDecoderInterface;
use Chubbyphp\Deserialization\DeserializerLogicException;
use Chubbyphp\Mock\Call;
use Chubbyphp\Mock\MockByCallsTrait;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Chubbyphp\Deserialization\Decoder\Decoder
 *
 * @internal
 */
final class DecoderTest extends TestCase
{
    use MockByCallsTrait;

    public function testGetContentTypes(): void
    {
        /** @var MockObject|TypeDecoderInterface */
        $typeDecoder = $this->getMockByCalls(TypeDecoderInterface::class, [
            Call::create('getContentType')->with()->willReturn('application/json'),
        ]);

        $decoder = new Decoder([$typeDecoder]);

        error_clear_last();

        self::assertSame(['application/json'], $decoder->getContentTypes());

        $error = error_get_last();

        self::assertNotNull($error);

        self::assertSame(E_USER_DEPRECATED, $error['type']);
        self::assertSame(sprintf(
            '%s:getContentTypes use %s:getContentTypes',
            Decoder::class,
            BaseDecoder::class
        ), $error['message']);
    }

    public function testDecode(): void
    {
        /** @var MockObject|TypeDecoderInterface */
        $typeDecoder = $this->getMockByCalls(TypeDecoderInterface::class, [
            Call::create('getContentType')->with()->willReturn('application/json'),
            Call::create('decode')->with('{"key": "value"}')->willReturn(['key' => 'value']),
        ]);

        $decoder = new Decoder([$typeDecoder]);

        self::assertSame(['key' => 'value'], $decoder->decode('{"key": "value"}', 'application/json'));
    }

    public function testDecodeWithMissingType(): void
    {
        $this->expectException(DeserializerLogicException::class);
        $this->expectExceptionMessage('There is no decoder/encoder for content-type: "application/xml"');

        /** @var MockObject|TypeDecoderInterface */
        $typeDecoder = $this->getMockByCalls(TypeDecoderInterface::class, [
            Call::create('getContentType')->with()->willReturn('application/json'),
        ]);

        $decoder = new Decoder([$typeDecoder]);

        $decoder->decode('<key>value</key>', 'application/xml');
    }
}
