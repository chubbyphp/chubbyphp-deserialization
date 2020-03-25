<?php

declare(strict_types=1);

namespace Chubbyphp\Tests\Deserialization\Unit\Policy;

use Chubbyphp\Deserialization\Denormalizer\DenormalizerContextInterface;
use Chubbyphp\Deserialization\Policy\NullPolicy;
use Chubbyphp\Mock\MockByCallsTrait;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Chubbyphp\Deserialization\Policy\NullPolicy
 *
 * @internal
 */
final class NullPolicyTest extends TestCase
{
    use MockByCallsTrait;

    public function testIsCompliantReturnsTrue(): void
    {
        $object = new \stdClass();

        /** @var DenormalizerContextInterface|MockObject $context */
        $context = $this->getMockByCalls(DenormalizerContextInterface::class);

        $policy = new NullPolicy();

        self::assertTrue($policy->isCompliant($context, $object));
    }

    public function testIsCompliantIncludingReturnsTrue(): void
    {
        $object = new \stdClass();

        $path = '';

        /** @var DenormalizerContextInterface|MockObject $context */
        $context = $this->getMockByCalls(DenormalizerContextInterface::class);

        $policy = new NullPolicy();

        self::assertTrue($policy->isCompliantIncludingPath($object, $context, $path));
    }
}
