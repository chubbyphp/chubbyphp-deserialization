<?php

declare(strict_types=1);

namespace Chubbyphp\Tests\Deserialization\Policy;

use Chubbyphp\Deserialization\Denormalizer\DenormalizerContextInterface;
use Chubbyphp\Deserialization\Policy\CallbackPolicy;
use Chubbyphp\Mock\MockByCallsTrait;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Chubbyphp\Deserialization\Policy\CallbackPolicy
 *
 * @internal
 */
class CallbackPolicyTest extends TestCase
{
    use MockByCallsTrait;

    public function testIsCompliantReturnsTrueIfCallbackReturnsTrue()
    {
        $object = new \stdClass();

        /** @var DenormalizerContextInterface|MockObject $context */
        $context = $this->getMockByCalls(DenormalizerContextInterface::class, []);

        $policy = new CallbackPolicy(function ($contextParameter, $objectParameter) use ($context, $object) {
            self::assertSame($context, $contextParameter);
            self::assertSame($object, $objectParameter);

            return true;
        });

        self::assertTrue($policy->isCompliant($context, $object));
    }

    public function testIsCompliantReturnsFalseIfCallbackReturnsFalse()
    {
        $object = new \stdClass();

        /** @var DenormalizerContextInterface|MockObject $context */
        $context = $this->getMockByCalls(DenormalizerContextInterface::class, []);

        $policy = new CallbackPolicy(function ($contextParameter, $objectParameter) use ($context, $object) {
            self::assertSame($context, $contextParameter);
            self::assertSame($object, $objectParameter);

            return false;
        });

        self::assertFalse($policy->isCompliant($context, $object));
    }
}
