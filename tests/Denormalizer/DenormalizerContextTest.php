<?php

declare(strict_types=1);

namespace Chubbyphp\Tests\Deserialization\Denormalizer;

use Chubbyphp\Deserialization\Denormalizer\DenormalizerContext;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ServerRequestInterface;

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
        self::assertNull($context->getRequest());
    }

    public function testCreateWithOverridenSettings()
    {
        $request = $this->getRequest();

        $context = new DenormalizerContext(true, ['group1'], $request);

        self::assertSame(true, $context->isAllowedAdditionalFields());
        self::assertSame(['group1'], $context->getGroups());
        self::assertSame($request, $context->getRequest());
    }

    /**
     * @return ServerRequestInterface
     */
    private function getRequest(): ServerRequestInterface
    {
        /** @var ServerRequestInterface|\PHPUnit_Framework_MockObject_MockObject $request */
        $request = $this->getMockBuilder(ServerRequestInterface::class)->getMockForAbstractClass();

        return $request;
    }
}
