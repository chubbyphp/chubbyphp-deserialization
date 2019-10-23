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
        self::assertSame([], $context->getGroups());
        self::assertNull($context->getRequest());
        self::assertFalse($context->isResetMissingFields());
        self::assertSame([], $context->getAttributes());
    }

    public function testCreateWithSetResetMissingField(): void
    {
        error_clear_last();

        DenormalizerContextBuilder::create()->setResetMissingFields(true);

        $error = error_get_last();

        self::assertNotNull($error);

        self::assertSame(E_USER_DEPRECATED, $error['type']);
        self::assertSame(
            'setResetMissingFields is broken by design, please do this your self by model or repository',
            $error['message']
        );
    }

    public function testCreateWithOverridenSettings(): void
    {
        /** @var ServerRequestInterface|MockObject $request */
        $request = $this->getMockByCalls(ServerRequestInterface::class);

        $context = DenormalizerContextBuilder::create()
            ->setAllowedAdditionalFields(['allowed_field'])
            ->setGroups(['group1'])
            ->setRequest($request)
            ->setAttributes(['attribute' => 'value'])
            ->getContext()
        ;

        self::assertInstanceOf(DenormalizerContextInterface::class, $context);

        self::assertSame(['allowed_field'], $context->getAllowedAdditionalFields());
        self::assertSame(['group1'], $context->getGroups());
        self::assertSame($request, $context->getRequest());
        self::assertFalse($context->isResetMissingFields());
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
