<?php

declare(strict_types=1);

namespace Chubbyphp\Tests\Deserialization;

use Chubbyphp\Deserialization\DeserializerLogicException;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Chubbyphp\Deserialization\DeserializerLogicException
 */
class DeserializerLogicExceptionTest extends TestCase
{
    public function testCreateMissingContentType()
    {
        $exception = DeserializerLogicException::createMissingContentType('application/json');

        self::assertSame('There is no decoder for content-type: application/json', $exception->getMessage());
    }

    public function testCreateMissingMapping()
    {
        $exception = DeserializerLogicException::createMissingMapping(\stdClass::class);

        self::assertSame('There is no mapping for class: stdClass', $exception->getMessage());
    }

    public function testCreateMissingMethod()
    {
        $exception = DeserializerLogicException::createMissingMethod(\stdClass::class, ['getName', 'hasName']);

        self::assertSame(
            'There are no accessible method(s) "getName", "hasName", within class: stdClass',
            $exception->getMessage()
        );
    }

    public function testCreateNotParsable()
    {
        $exception = DeserializerLogicException::createMissingProperty(\stdClass::class, 'name');

        self::assertSame('There is no property "name" within class: stdClass', $exception->getMessage());
    }
}
