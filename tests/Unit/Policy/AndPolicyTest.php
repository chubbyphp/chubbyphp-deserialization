<?php

declare(strict_types=1);

namespace Chubbyphp\Tests\Deserialization\Unit\Policy;

use Chubbyphp\Deserialization\Denormalizer\DenormalizerContextInterface;
use Chubbyphp\Deserialization\Policy\AndPolicy;
use Chubbyphp\Deserialization\Policy\PolicyInterface;
use Chubbyphp\Mock\Call;
use Chubbyphp\Mock\MockByCallsTrait;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Chubbyphp\Deserialization\Policy\AndPolicy
 *
 * @internal
 */
final class AndPolicyTest extends TestCase
{
    use MockByCallsTrait;

    public function testIsCompliantIncludingPathReturnsTrueWithMultipleCompliantPolicies(): void
    {
        $object = new \stdClass();

        $path = '';

        /** @var DenormalizerContextInterface|MockObject $context */
        $context = $this->getMockByCalls(DenormalizerContextInterface::class);

        /** @var PolicyInterface|MockObject $compliantPolicy1 */
        $compliantPolicy1 = $this->getMockByCalls(PolicyInterface::class, [
            Call::create('isCompliant')->with($path, $object, $context)->willReturn(true),
        ]);

        /** @var PolicyInterface|MockObject $compliantPolicy2 */
        $compliantPolicy2 = $this->getMockByCalls(PolicyInterface::class, [
            Call::create('isCompliant')->with($path, $object, $context)->willReturn(true),
        ]);

        $policy = new AndPolicy([$compliantPolicy1, $compliantPolicy2]);

        self::assertTrue($policy->isCompliant($path, $object, $context));
    }

    public function testIsCompliantIncludingPathReturnsFalseWithNonCompliantIncludingPathPolicy(): void
    {
        $object = new \stdClass();

        $path = '';

        /** @var DenormalizerContextInterface|MockObject $context */
        $context = $this->getMockByCalls(DenormalizerContextInterface::class);

        /** @var PolicyInterface|MockObject $compliantPolicy */
        $compliantPolicy = $this->getMockByCalls(PolicyInterface::class, [
            Call::create('isCompliant')->with($path, $object, $context)->willReturn(true),
        ]);

        /** @var PolicyInterface|MockObject $compliantPolicy2 */
        $nonCompliantPolicy = $this->getMockByCalls(PolicyInterface::class, [
            Call::create('isCompliant')->with($path, $object, $context)->willReturn(false),
        ]);

        /** @var PolicyInterface|MockObject $notExpectedToBeCalledPolicy */
        $notExpectedToBeCalledPolicy = $this->getMockByCalls(PolicyInterface::class);

        $policy = new AndPolicy([$compliantPolicy, $nonCompliantPolicy, $notExpectedToBeCalledPolicy]);

        self::assertFalse($policy->isCompliant($path, $object, $context));
    }
}
