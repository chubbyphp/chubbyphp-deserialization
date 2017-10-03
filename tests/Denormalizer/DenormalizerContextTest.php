<?php

declare(strict_types=1);

namespace Chubbyphp\Tests\Deserialization\Denormalizer;

use Chubbyphp\Deserialization\Denormalizer\DenormalizerContext;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Chubbyphp\Deserialization\Denormalizer\DenormalizerContext
 */
class DenormalizerContextTest extends TestCase
{
    public function testCreate()
    {
        $context = new DenormalizerContext();

        self::assertSame(false, $context->isAllowedAdditionalFields());
        self::assertSame([], $context->getGroups());
    }

    public function testCreateWithOverridenSettings()
    {
        $context = new DenormalizerContext(true, ['group1']);

        self::assertSame(true, $context->isAllowedAdditionalFields());
        self::assertSame(['group1'], $context->getGroups());
    }
}
