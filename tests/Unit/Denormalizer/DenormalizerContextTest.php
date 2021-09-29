<?php

declare(strict_types=1);

namespace Chubbyphp\Tests\Deserialization\Unit\Denormalizer;

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
final class DenormalizerContextTest extends TestCase
{
    use MockByCallsTrait;

    public function testCreate(): void
    {
        $context = new DenormalizerContext();

        self::assertNull($context->getAllowedAdditionalFields());
        self::assertNull($context->getRequest());
        self::assertFalse($context->isClearMissing());
        self::assertSame([], $context->getAttributes());
        self::assertNull($context->getAttribute('nonExistingAttribute'));
        self::assertSame('default', $context->getAttribute('nonExistingAttribute', 'default'));
    }

    public function testCreateWithOverridenSettings(): void
    {
        /** @var MockObject|ServerRequestInterface $request */
        $request = $this->getMockByCalls(ServerRequestInterface::class);

        $context = new DenormalizerContext(
            $request,
            ['attribute' => 'value'],
            ['allowed_field'],
            true
        );

        self::assertSame(['allowed_field'], $context->getAllowedAdditionalFields());
        self::assertSame($request, $context->getRequest());
        self::assertTrue($context->isClearMissing());
        self::assertSame(['attribute' => 'value'], $context->getAttributes());
        self::assertSame('value', $context->getAttribute('attribute'));
    }

    public function testWithAttributes(): void
    {
        /** @var MockObject|ServerRequestInterface $request */
        $request = $this->getMockByCalls(ServerRequestInterface::class);

        $context = new DenormalizerContext($request, ['attribute' => 'value'], ['allowed_field']);

        $newContext = $context->withAttributes(['otherAttribute' => 'value2']);

        self::assertNotSame($context, $newContext);

        self::assertSame(['otherAttribute' => 'value2'], $newContext->getAttributes());
        self::assertSame(['attribute' => 'value'], $context->getAttributes());
    }

    public function testWithAttribute(): void
    {
        /** @var MockObject|ServerRequestInterface $request */
        $request = $this->getMockByCalls(ServerRequestInterface::class);

        $context = new DenormalizerContext($request, ['attribute' => 'value'], ['allowed_field']);

        $newContext = $context->withAttribute('otherAttribute', 'value2');

        self::assertNotSame($context, $newContext);

        self::assertSame(['attribute' => 'value', 'otherAttribute' => 'value2'], $newContext->getAttributes());
        self::assertSame(['attribute' => 'value'], $context->getAttributes());
    }
}
