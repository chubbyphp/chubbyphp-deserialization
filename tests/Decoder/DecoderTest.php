<?php

declare(strict_types=1);

namespace Chubbyphp\Tests\Deserialization\Decoder;

use Chubbyphp\Deserialization\Decoder\Decoder;
use Chubbyphp\Deserialization\Decoder\TypeDecoderInterface;
use Chubbyphp\Deserialization\DeserializerLogicException;
use Chubbyphp\Mock\Call;
use Chubbyphp\Mock\MockByCallsTrait;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Chubbyphp\Deserialization\Decoder\Decoder
 */
class DecoderTest extends TestCase
{
    use MockByCallsTrait;

    public function testGetContentTypes()
    {
        /** @var TypeDecoderInterface|MockObject */
        $typeDecoder = $this->getMockByCalls(TypeDecoderInterface::class, [
            Call::create('getContentType')->with()->willReturn('application/json'),
        ]);

        $decoder = new Decoder([$typeDecoder]);

        self::assertSame(['application/json'], $decoder->getContentTypes());
    }

    public function testDecode()
    {
        /** @var TypeDecoderInterface|MockObject */
        $typeDecoder = $this->getMockByCalls(TypeDecoderInterface::class, [
            Call::create('getContentType')->with()->willReturn('application/json'),
            Call::create('decode')->with('{"key": "value"}')->willReturn(['key' => 'value']),
        ]);

        $decoder = new Decoder([$typeDecoder]);

        self::assertSame(['key' => 'value'], $decoder->decode('{"key": "value"}', 'application/json'));
    }

    public function testDecodeWithMissingType()
    {
        $this->expectException(DeserializerLogicException::class);
        $this->expectExceptionMessage('There is no decoder for content-type: "application/xml"');

        /** @var TypeDecoderInterface|MockObject */
        $typeDecoder = $this->getMockByCalls(TypeDecoderInterface::class, [
            Call::create('getContentType')->with()->willReturn('application/json'),
        ]);

        $decoder = new Decoder([$typeDecoder]);

        $decoder->decode('<key>value</key>', 'application/xml');
    }
}
