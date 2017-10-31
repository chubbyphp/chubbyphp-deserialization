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

    public function testCreateNotParsable()
    {
        $exception = DeserializerRuntimeException::createNotParsable('application/json');

        self::assertSame('Data is not parsable with content-type: "application/json"', $exception->getMessage());
    }

    public function testCreateNotAllowedAddtionalFields()
    {
        $exception = DeserializerRuntimeException::createNotAllowedAdditionalFields(['path1', 'path2']);

        self::assertSame('There are additional field(s) at paths: "path1", "path2"', $exception->getMessage());
    }

    public function testCreateMissingObjectType()
    {
        $exception = DeserializerRuntimeException::createMissingObjectType('path1', ['model']);

        self::assertSame('Missing object type, supported are "model" at path: "path1"', $exception->getMessage());
    }

    public function testCreateInvalidObjectType()
    {
        $exception = DeserializerRuntimeException::createInvalidObjectType('path1', 'unknown', ['model']);

        self::assertSame('Unsupported object type "unknown", supported are "model" at path: "path1"', $exception->getMessage());
    }

    public function testCreateDataContainsNumericKey()
    {
        $exception = DeserializerRuntimeException::createDataContainsNumericKey('path1', [0]);

        self::assertSame(
            'The data contains numeric key(s) "0" at path: "path1"',
            $exception->getMessage()
        );
    }
}
