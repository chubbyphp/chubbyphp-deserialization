<?php

declare(strict_types=1);

namespace Chubbyphp\Tests\Deserialization\Denormalizer;

use Chubbyphp\Deserialization\Denormalizer\DenormalizerContextBuilder;
use Chubbyphp\Deserialization\Denormalizer\DenormalizerContextInterface;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ServerRequestInterface;

/**
 * @covers \Chubbyphp\Deserialization\Denormalizer\DenormalizerContextBuilder
 */
class DenormalizerContextBuilderTest extends TestCase
{
    public function testCreate()
    {
        $context = DenormalizerContextBuilder::create()->getContext();

        self::assertInstanceOf(DenormalizerContextInterface::class, $context);

        self::assertSame(null, $context->getAllowedAdditionalFields());
        self::assertSame([], $context->getGroups());
        self::assertNull($context->getRequest());
    }

    public function testCreateWithOverridenSettings()
    {
        $request = $this->getRequest();

        $context = DenormalizerContextBuilder::create()
            ->setAllowedAdditionalFields(['allowed_field'])
            ->setGroups(['group1'])
            ->setRequest($request)
            ->getContext();

        self::assertInstanceOf(DenormalizerContextInterface::class, $context);

        self::assertSame(['allowed_field'], $context->getAllowedAdditionalFields());
        self::assertSame(['group1'], $context->getGroups());
        self::assertSame($request, $context->getRequest());
    }

    public function testCreateSetNullRequest()
    {
        $context = DenormalizerContextBuilder::create()
            ->setRequest()
            ->getContext();

        self::assertInstanceOf(DenormalizerContextInterface::class, $context);

        self::assertNull($context->getRequest());
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
