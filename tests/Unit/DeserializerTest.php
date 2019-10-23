<?php

declare(strict_types=1);

namespace Chubbyphp\Tests\Deserialization\Unit;

use Chubbyphp\Deserialization\Decoder\DecoderInterface;
use Chubbyphp\Deserialization\Denormalizer\DenormalizerContextInterface;
use Chubbyphp\Deserialization\Denormalizer\DenormalizerInterface;
use Chubbyphp\Deserialization\Deserializer;
use Chubbyphp\Mock\Call;
use Chubbyphp\Mock\MockByCallsTrait;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Chubbyphp\Deserialization\Deserializer
 *
 * @internal
 */
final class DeserializerTest extends TestCase
{
    use MockByCallsTrait;

    public function testDeserialize(): void
    {
        $object = new \stdClass();

        /** @var DenormalizerContextInterface|MockObject $context */
        $context = $this->getMockByCalls(DenormalizerContextInterface::class);

        /** @var DecoderInterface|MockObject $decoder */
        $decoder = $this->getMockByCalls(DecoderInterface::class, [
            Call::create('decode')->with('{"name": "php"}', 'application/json')->willReturn(['name' => 'php']),
        ]);

        /** @var DenormalizerInterface|MockObject $denormalizer */
        $denormalizer = $this->getMockByCalls(DenormalizerInterface::class, [
            Call::create('denormalize')->with($object, ['name' => 'php'], $context, '')->willReturn($object),
        ]);

        $deserializer = new Deserializer($decoder, $denormalizer);

        self::assertSame($object, $deserializer->deserialize($object, '{"name": "php"}', 'application/json', $context));
    }

    public function testDecode(): void
    {
        /** @var DecoderInterface|MockObject $decoder */
        $decoder = $this->getMockByCalls(DecoderInterface::class, [
            Call::create('decode')->with('{"name": "php"}', 'application/json')->willReturn(['name' => 'php']),
        ]);

        /** @var DenormalizerInterface|MockObject $denormalizer */
        $denormalizer = $this->getMockByCalls(DenormalizerInterface::class);

        $deserializer = new Deserializer($decoder, $denormalizer);

        self::assertEquals(['name' => 'php'], $deserializer->decode('{"name": "php"}', 'application/json'));
    }

    public function testGetContentTypes(): void
    {
        /** @var DecoderInterface|MockObject $decoder */
        $decoder = $this->getMockByCalls(DecoderInterface::class, [
            Call::create('getContentTypes')->with()->willReturn(['application/json']),
        ]);

        /** @var DenormalizerInterface|MockObject $denormalizer */
        $denormalizer = $this->getMockByCalls(DenormalizerInterface::class);

        $deserializer = new Deserializer($decoder, $denormalizer);

        self::assertEquals(['application/json'], $deserializer->getContentTypes());
    }

    public function testDenormalize(): void
    {
        $object = new \stdClass();

        /** @var DenormalizerContextInterface|MockObject $context */
        $context = $this->getMockByCalls(DenormalizerContextInterface::class);

        /** @var DecoderInterface|MockObject $decoder */
        $decoder = $this->getMockByCalls(DecoderInterface::class);

        /** @var DenormalizerInterface|MockObject $denormalizer */
        $denormalizer = $this->getMockByCalls(DenormalizerInterface::class, [
            Call::create('denormalize')->with($object, ['name' => 'php'], $context, '')->willReturn($object),
        ]);

        $deserializer = new Deserializer($decoder, $denormalizer);

        self::assertSame($object, $deserializer->denormalize($object, ['name' => 'php'], $context));
    }
}
