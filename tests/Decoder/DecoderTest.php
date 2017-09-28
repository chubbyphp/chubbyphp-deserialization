<?php

declare(strict_types=1);

namespace Chubbyphp\Tests\Deserialization\Decoder;

use Chubbyphp\Deserialization\Decoder\Decoder;
use Chubbyphp\Deserialization\Decoder\DecoderTypeInterface;
use Chubbyphp\Deserialization\DeserializerLogicException;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Chubbyphp\Deserialization\Decoder\Decoder
 */
class DecoderTest extends TestCase
{
    public function testGetContentTypes()
    {
        $decoder = new Decoder([$this->getDecoderType()]);

        self::assertSame(['application/json'], $decoder->getContentTypes());
    }

    public function testDecode()
    {
        $decoder = new Decoder([$this->getDecoderType()]);

        self::assertSame(['key' => 'value'], $decoder->decode('{"key": "value"}', 'application/json'));
    }

    public function testDecodeWithMissingType()
    {
        self::expectException(DeserializerLogicException::class);
        self::expectExceptionMessage('There is no decoder for content-type: application/xml');

        $decoder = new Decoder([$this->getDecoderType()]);

        $decoder->decode('<key>value</key>', 'application/xml');
    }

    /**
     * @return DecoderTypeInterface
     */
    private function getDecoderType(): DecoderTypeInterface
    {
        /** @var DecoderTypeInterface|\PHPUnit_Framework_MockObject_MockObject $decoderType */
        $decoderType = $this->getMockBuilder(DecoderTypeInterface::class)
            ->setMethods(['getContentType', 'decode'])
            ->getMockForAbstractClass();

        $decoderType->expects(self::any())->method('getContentType')->willReturn('application/json');
        $decoderType->expects(self::any())->method('decode')->willReturnCallback(function (string $data) {
            return json_decode($data, true);
        });

        return $decoderType;
    }
}
