<?php

declare(strict_types=1);

namespace Chubbyphp\Tests\Deserialization\Mapping;

use Chubbyphp\Deserialization\Mapping\DenormalizationClassToTypeMapping;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Chubbyphp\Deserialization\Mapping\DenormalizationClassToTypeMapping
 */
class DenormalizationClassToTypeMappingTest extends TestCase
{
    public function testGetClass()
    {
        $classToTypeMapping = new DenormalizationClassToTypeMapping(\stdClass::class, ['model']);

        self::assertSame(\stdClass::class, $classToTypeMapping->getClass());
    }

    public function testGetTypes()
    {
        $classToTypeMapping = new DenormalizationClassToTypeMapping(\stdClass::class, ['model']);

        self::assertSame(['model'], $classToTypeMapping->getTypes());
    }
}
