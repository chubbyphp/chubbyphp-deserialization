<?php

declare(strict_types=1);

namespace Chubbyphp\Tests\Deserialization\Denormalizer;

use Chubbyphp\Deserialization\Denormalizer\DenormalizerContextBuilder;
use Chubbyphp\Deserialization\Denormalizer\DenormalizerContextInterface;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Chubbyphp\Deserialization\Denormalizer\DenormalizerContextBuilder
 */
class DenormalizerContextBuilderTest extends TestCase
{
    public function testCreate()
    {
        $context = DenormalizerContextBuilder::create()->getContext();

        self::assertInstanceOf(DenormalizerContextInterface::class, $context);

        self::assertSame(false, $context->isAllowedAdditionalFields());
        self::assertSame([], $context->getGroups());
    }

    public function testCreateWithOverridenSettings()
    {
        $context = DenormalizerContextBuilder::create()
            ->setAllowedAdditionalFields(true)
            ->setGroups(['group1'])
            ->getContext();

        self::assertInstanceOf(DenormalizerContextInterface::class, $context);

        self::assertSame(true, $context->isAllowedAdditionalFields());
        self::assertSame(['group1'], $context->getGroups());
    }
}
