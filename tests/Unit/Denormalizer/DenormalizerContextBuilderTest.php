<?php

declare(strict_types=1);

namespace Chubbyphp\Tests\Deserialization\Unit\Denormalizer;

use Chubbyphp\Deserialization\Denormalizer\DenormalizerContextBuilder;
use Chubbyphp\Deserialization\Denormalizer\DenormalizerContextInterface;
use Chubbyphp\Mock\MockByCallsTrait;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ServerRequestInterface;

/**
 * @covers \Chubbyphp\Deserialization\Denormalizer\DenormalizerContextBuilder
 *
 * @internal
 */
final class DenormalizerContextBuilderTest extends TestCase
{
    use MockByCallsTrait;

    public function testCreate(): void
    {
        $context = DenormalizerContextBuilder::create()->getContext();

        self::assertInstanceOf(DenormalizerContextInterface::class, $context);

        self::assertSame(null, $context->getAllowedAdditionalFields());
        self::assertNull($context->getRequest());
        self::assertFalse($context->isClearMissing());
        self::assertSame([], $context->getAttributes());
    }

    public function testCreateWithOverridenSettings(): void
    {
        /** @var ServerRequestInterface|MockObject $request */
        $request = $this->getMockByCalls(ServerRequestInterface::class);

        $context = DenormalizerContextBuilder::create()
            ->setAllowedAdditionalFields(['allowed_field'])
            ->setRequest($request)
            ->setClearMissing(true)
            ->setAttributes(['attribute' => 'value'])
            ->getContext()
        ;

        self::assertInstanceOf(DenormalizerContextInterface::class, $context);

        self::assertSame(['allowed_field'], $context->getAllowedAdditionalFields());
        self::assertSame($request, $context->getRequest());
        self::assertTrue($context->isClearMissing());
        self::assertSame(['attribute' => 'value'], $context->getAttributes());
    }

    public function testCreateSetNullRequest(): void
    {
        $context = DenormalizerContextBuilder::create()
            ->setRequest()
            ->getContext()
        ;

        self::assertInstanceOf(DenormalizerContextInterface::class, $context);

        self::assertNull($context->getRequest());
    }
}
