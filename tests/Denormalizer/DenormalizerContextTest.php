<?php

declare(strict_types=1);

namespace Chubbyphp\Tests\Deserialization\Denormalizer;

use Chubbyphp\Deserialization\Denormalizer\DenormalizerContext;
use Chubbyphp\Mock\MockByCallsTrait;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ServerRequestInterface;

/**
 * @covers \Chubbyphp\Deserialization\Denormalizer\DenormalizerContext
 *
 * @internal
 */
class DenormalizerContextTest extends TestCase
{
    use MockByCallsTrait;

    public function testCreate()
    {
        $context = new DenormalizerContext();

        self::assertSame(null, $context->getAllowedAdditionalFields());
        self::assertSame([], $context->getGroups());
        self::assertNull($context->getRequest());
        self::assertFalse($context->isResetMissingFields());
    }

    public function testCreateWithOverridenSettings()
    {
        /** @var ServerRequestInterface|MockObject $request */
        $request = $this->getMockByCalls(ServerRequestInterface::class);

        $context = new DenormalizerContext(['allowed_field'], ['group1'], $request, false);

        self::assertSame(['allowed_field'], $context->getAllowedAdditionalFields());
        self::assertSame(['group1'], $context->getGroups());
        self::assertSame($request, $context->getRequest());
        self::assertFalse($context->isResetMissingFields());
    }

    public function testWithResetMissingFieldsExpectDeprecation()
    {
        error_clear_last();

        $context = new DenormalizerContext(null, [], null, true);

        self::assertTrue($context->isResetMissingFields());

        $error = error_get_last();

        self::assertNotNull($error);

        self::assertSame(E_USER_DEPRECATED, $error['type']);
        self::assertSame('resetMissingFields is broken by design, please do this your self by model or repository', $error['message']);
    }
}
