<?php

declare(strict_types=1);

namespace Chubbyphp\Tests\Deserialization\Decoder;

use Chubbyphp\Deserialization\Decoder\DecoderException;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Chubbyphp\Deserialization\Decoder\DecoderException
 */
class DecoderExceptionTest extends TestCase
{
    public function testCreateMissing()
    {
        $exception = DecoderException::createMissing('application/json');

        self::assertSame('There is no decoder for content-type: application/json', $exception->getMessage());
    }

    public function testCreateNotParsable()
    {
        $exception = DecoderException::createNotParsable('application/json');

        self::assertSame('Data is not parsable with content-type: application/json', $exception->getMessage());
    }
}
