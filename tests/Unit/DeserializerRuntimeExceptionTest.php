<?php

declare(strict_types=1);

namespace Chubbyphp\Tests\Deserialization\Unit;

use Chubbyphp\Deserialization\DeserializerRuntimeException;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Chubbyphp\Deserialization\DeserializerRuntimeException
 *
 * @internal
 */
final class DeserializerRuntimeExceptionTest extends TestCase
{
    public function testCreateInvalidDataType(): void
    {
        $exception = DeserializerRuntimeException::createInvalidDataType('path1', 'null', 'array');

        self::assertSame('There is an invalid data type "null", needed "array" at path: "path1"', $exception->getMessage());
    }

    public function testCreateNotParsableWithoutError(): void
    {
        $exception = DeserializerRuntimeException::createNotParsable('application/json');

        self::assertSame('Data is not parsable with content-type: "application/json"', $exception->getMessage());
    }

    public function testCreateNotParsableWithError(): void
    {
        $exception = DeserializerRuntimeException::createNotParsable('application/json', 'unknown');

        self::assertSame(
            'Data is not parsable with content-type: "application/json", error: "unknown"',
            $exception->getMessage()
        );
    }

    public function testCreateNotAllowedAddtionalFields(): void
    {
        $exception = DeserializerRuntimeException::createNotAllowedAdditionalFields(['path1', 'path2']);

        self::assertSame('There are additional field(s) at paths: "path1", "path2"', $exception->getMessage());
    }

    public function testCreateMissingObjectType(): void
    {
        $exception = DeserializerRuntimeException::createMissingObjectType('path1', ['model']);

        self::assertSame('Missing object type, supported are "model" at path: "path1"', $exception->getMessage());
    }

    public function testCreateInvalidObjectType(): void
    {
        $exception = DeserializerRuntimeException::createInvalidObjectType('path1', 'unknown', ['model']);

        self::assertSame('Unsupported object type "unknown", supported are "model" at path: "path1"', $exception->getMessage());
    }

    public function testCreateTypeIsNotAString(): void
    {
        $exception = DeserializerRuntimeException::createTypeIsNotAString('path', 'array');

        self::assertSame('Type is not a string, "array" given at path: "path"', $exception->getMessage());
    }
}
