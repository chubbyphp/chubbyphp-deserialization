<?php

declare(strict_types=1);

namespace Chubbyphp\Tests\Deserialization\Accessor;

use Chubbyphp\Deserialization\Accessor\AccessorException;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Chubbyphp\Deserialization\Accessor\AccessorException
 */
class AccessorExceptionTest extends TestCase
{
    public function testCreateMissing()
    {
        $exception = AccessorException::createMissingMethod(\stdClass::class, ['setName']);

        self::assertSame('Class stdClass does not contain an accessable method(s) setName', $exception->getMessage());
    }

    public function testCreateNotParsable()
    {
        $exception = AccessorException::createMissingProperty(\stdClass::class, 'name');

        self::assertSame('Class stdClass does not contain property name', $exception->getMessage());
    }
}
