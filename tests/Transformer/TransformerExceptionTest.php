<?php

declare(strict_types=1);

namespace Chubbyphp\Tests\Deserialization\Transformer;

use Chubbyphp\Deserialization\Transformer\TransformerException;

/**
 * @covers \Chubbyphp\Deserialization\Transformer\TransformerException
 */
class TransformerExceptionTest extends \PHPUnit_Framework_TestCase
{
    public function testCreate()
    {
        $exception = TransformerException::create('cannot parse');

        self::assertSame('Transform error: cannot parse', $exception->getMessage());
    }
}
