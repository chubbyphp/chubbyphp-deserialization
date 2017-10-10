<?php

declare(strict_types=1);

namespace Chubbyphp\Tests\Deserialization;

use Chubbyphp\Deserialization\DeserializerRuntimeException;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Chubbyphp\Deserialization\DeserializerRuntimeException
 */
class DeserializerRuntimeExceptionTest extends TestCase
{
    public function testCreateInvalidDataType()
    {
        $exception = DeserializerRuntimeException::createInvalidDataType('path1', 'null', 'array');

        self::assertSame('There is an invalid data type "null", needed "array" at path: "path1"', $exception->getMessage());
    }

    public function testCreateInvalidObjectType()
    {
        $exception = DeserializerRuntimeException::createInvalidObjectType('path1', 'child', ['parent']);

        self::assertSame('There is an invalid object type "child", allowed types are "parent" at path: "path1"', $exception->getMessage());
    }

    public function testCreateMissingObjectType()
    {
        $exception = DeserializerRuntimeException::createMissingObjectType('path1', ['parent']);

        self::assertSame('Missing object type, allowed types are "parent" at path: "path1"', $exception->getMessage());
    }

    public function testCreateNotParsable()
    {
        $exception = DeserializerRuntimeException::createNotParsable('application/json');

        self::assertSame('Data is not parsable with content-type: "application/json"', $exception->getMessage());
    }

    public function testCreateNotAllowedAddtionalFields()
    {
        $exception = DeserializerRuntimeException::createNotAllowedAddtionalFields(['path1', 'path2']);

        self::assertSame('There are additional field(s) at paths: "path1", "path2"', $exception->getMessage());
    }
}
