<?php

declare(strict_types=1);

namespace Chubbyphp\Tests\Deserialization\Policy;

use Chubbyphp\Mock\Call;
use Chubbyphp\Mock\MockByCallsTrait;
use Chubbyphp\Deserialization\Denormalizer\DenormalizerContextInterface;
use Chubbyphp\Deserialization\Policy\NotPolicy;
use Chubbyphp\Deserialization\Policy\PolicyInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Chubbyphp\Deserialization\Policy\NotPolicy
 */
class NotPolicyTest extends TestCase
{
    use MockByCallsTrait;

    public function testIsCompliantReturnsTrueIfGivenPolicyReturnsFalse()
    {
        $object = new \stdClass();

        /** @var DenormalizerContextInterface|MockObject $context */
        $context = $this->getMockByCalls(DenormalizerContextInterface::class, []);

        /** @var PolicyInterface|MockObject $nonCompliantPolicy */
        $nonCompliantPolicy = $this->getMockByCalls(PolicyInterface::class, [
            Call::create('isCompliant')->with($context, $object)->willReturn(false),
        ]);

        $policy = new NotPolicy($nonCompliantPolicy);

        self::assertTrue($policy->isCompliant($context, $object));
    }

    public function testIsCompliantReturnsFalseIfGivenPolicyReturnsTrue()
    {
        $object = new \stdClass();

        /** @var DenormalizerContextInterface|MockObject $context */
        $context = $this->getMockByCalls(DenormalizerContextInterface::class, []);

        /** @var PolicyInterface|MockObject $nonCompliantPolicy */
        $nonCompliantPolicy = $this->getMockByCalls(PolicyInterface::class, [
            Call::create('isCompliant')->with($context, $object)->willReturn(true),
        ]);

        $policy = new NotPolicy($nonCompliantPolicy);

        self::assertFalse($policy->isCompliant($context, $object));
    }
}
