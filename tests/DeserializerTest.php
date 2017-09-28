<?php

declare(strict_types=1);

namespace Chubbyphp\Tests\Deserialization;

use Chubbyphp\Deserialization\Decoder\DecoderInterface;
use Chubbyphp\Deserialization\Denormalizer\DenormalizerContextInterface;
use Chubbyphp\Deserialization\Denormalizer\DenormalizerInterface;
use Chubbyphp\Deserialization\Deserializer;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Chubbyphp\Deserialization\Deserializer
 */
class DeserializerTest extends TestCase
{
    public function testDeserialize()
    {
        $deserializer = new Deserializer($this->getDecoder(), $this->getDenormalizer());

        $object = new \stdClass();

        $deserializer->deserialize(
            $object,
            '{"name": "Name"}',
            'application/json',
            $this->getDenormalizerContext()
        );

        self::assertSame('Name', $object->name);
    }

    /**
     * @return DecoderInterface
     */
    private function getDecoder(): DecoderInterface
    {
        /** @var DecoderInterface|\PHPUnit_Framework_MockObject_MockObject $decoder */
        $decoder = $this->getMockBuilder(DecoderInterface::class)->getMockForAbstractClass();

        $decoder->expects(self::any())->method('decode')->willReturnCallback(function (string $data, string $contentType) {
            self::assertSame('{"name": "Name"}', $data);
            self::assertSame('application/json', $contentType);

            return json_decode($data, true);
        });

        return $decoder;
    }

    /**
     * @return DenormalizerInterface
     */
    private function getDenormalizer(): DenormalizerInterface
    {
        /** @var DenormalizerInterface|\PHPUnit_Framework_MockObject_MockObject $decoder */
        $decoder = $this->getMockBuilder(DenormalizerInterface::class)->getMockForAbstractClass();

        $decoder->expects(self::any())->method('denormalize')->willReturnCallback(
            function ($object, array $data, DenormalizerContextInterface $context = null, string $path = '') {
                self::assertSame(['name' => 'Name'], $data);
                self::assertNotNull($context);
                self::assertSame('', $path);

                $object->name = $data['name'];

                return $object;
            }
        );

        return $decoder;
    }

    /**
     * @return DenormalizerContextInterface
     */
    private function getDenormalizerContext(): DenormalizerContextInterface
    {
        /** @var DenormalizerContextInterface|\PHPUnit_Framework_MockObject_MockObject $context */
        $context = $this->getMockBuilder(DenormalizerContextInterface::class)->getMockForAbstractClass();

        return $context;
    }
}
