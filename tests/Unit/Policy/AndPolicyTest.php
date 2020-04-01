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
    use PolicyIncludingPathTrait;

    public function testIsCompliantReturnsTrueWithMultipleCompliantPolicies(): void
    {
        error_clear_last();

        $object = new \stdClass();

        /** @var DenormalizerContextInterface|MockObject $context */
        $context = $this->getMockByCalls(DenormalizerContextInterface::class, []);

        /** @var PolicyInterface|MockObject $compliantPolicy1 */
        $compliantPolicy1 = $this->getMockByCalls(PolicyInterface::class, [
            Call::create('isCompliant')->with($context, $object)->willReturn(true),
        ]);

        /** @var PolicyInterface|MockObject $compliantPolicy2 */
        $compliantPolicy2 = $this->getMockByCalls(PolicyInterface::class, [
            Call::create('isCompliant')->with($context, $object)->willReturn(true),
        ]);

        $policy = new AndPolicy([$compliantPolicy1, $compliantPolicy2]);

        self::assertTrue($policy->isCompliant($context, $object));

        $error = error_get_last();

        self::assertNotNull($error);

        self::assertSame(E_USER_DEPRECATED, $error['type']);
        self::assertSame(
            sprintf('The %s::isCompliant method is deprecated ().', PolicyInterface::class),
            $error['message']
        );
    }

    public function testIsCompliantReturnsFalseWithNonCompliantPolicy(): void
    {
        error_clear_last();

        $object = new \stdClass();

        /** @var DenormalizerContextInterface|MockObject $context */
        $context = $this->getMockByCalls(DenormalizerContextInterface::class, []);

        /** @var PolicyInterface|MockObject $compliantPolicy1 */
        $compliantPolicy1 = $this->getMockByCalls(PolicyInterface::class, [
            Call::create('isCompliant')->with($context, $object)->willReturn(true),
        ]);

        /** @var PolicyInterface|MockObject $nonCompliantPolicy */
        $nonCompliantPolicy = $this->getMockByCalls(PolicyInterface::class, [
            Call::create('isCompliant')->with($context, $object)->willReturn(false),
        ]);

        /** @var PolicyInterface|MockObject $notExpectedToBeCalledPolicy */
        $notExpectedToBeCalledPolicy = $this->getMockByCalls(PolicyInterface::class);

        $policy = new AndPolicy([$compliantPolicy1, $nonCompliantPolicy, $notExpectedToBeCalledPolicy]);

        self::assertFalse($policy->isCompliant($context, $object));

        $error = error_get_last();

        self::assertNotNull($error);

        self::assertSame(E_USER_DEPRECATED, $error['type']);
        self::assertSame(
            sprintf('The %s::isCompliant method is deprecated ().', PolicyInterface::class),
            $error['message']
        );
    }

    public function testIsCompliantIncludingPathReturnsTrueWithMultipleCompliantPolicies(): void
    {
        error_clear_last();

        $object = new \stdClass();

        $path = '';

        /** @var DenormalizerContextInterface|MockObject $context */
        $context = $this->getMockByCalls(DenormalizerContextInterface::class, []);

        /** @var PolicyInterface|MockObject $compliantPolicy1 */
        $compliantPolicy1 = $this->getCompliantPolicyIncludingPath(true);

        /** @var PolicyInterface|MockObject $compliantPolicy2 */
        $compliantPolicy2 = $this->getCompliantPolicyIncludingPath(true);

        $policy = new AndPolicy([$compliantPolicy1, $compliantPolicy2]);

        self::assertTrue($policy->isCompliantIncludingPath($path, $object, $context));

        $error = error_get_last();

        self::assertNull($error);
    }

    public function testIsCompliantIncludingPathReturnsFalseWithNonCompliantIncludingPathPolicy(): void
    {
        error_clear_last();

        $object = new \stdClass();

        $path = '';

        /** @var DenormalizerContextInterface|MockObject $context */
        $context = $this->getMockByCalls(DenormalizerContextInterface::class, []);

        /** @var PolicyInterface|MockObject $compliantPolicy1 */
        $compliantPolicy1 = $this->getCompliantPolicyIncludingPath(true);

        /** @var PolicyInterface|MockObject $nonCompliantPolicy */
        $nonCompliantPolicy = $this->getCompliantPolicyIncludingPath(false);

        /** @var PolicyInterface|MockObject $notExpectedToBeCalledPolicy */
        $notExpectedToBeCalledPolicy = $this->getMockByCalls(PolicyInterface::class);

        $policy = new AndPolicy([$compliantPolicy1, $nonCompliantPolicy, $notExpectedToBeCalledPolicy]);

        self::assertFalse($policy->isCompliantIncludingPath($path, $object, $context));

        $error = error_get_last();

        self::assertNull($error);
    }

    public function testIsCompliantIncludingPathReturnsFalseWithNonCompliantPolicy(): void
    {
        error_clear_last();

        $object = new \stdClass();

        $path = '';

        /** @var DenormalizerContextInterface|MockObject $context */
        $context = $this->getMockByCalls(DenormalizerContextInterface::class, []);

        /** @var PolicyInterface|MockObject $compliantPolicy1 */
        $compliantPolicy1 = $this->getCompliantPolicyIncludingPath(true);

        /** @var PolicyInterface|MockObject $nonCompliantPolicy */
        $nonCompliantPolicy = $this->getMockByCalls(PolicyInterface::class, [
            Call::create('isCompliant')->with($context, $object)->willReturn(false),
        ]);

        /** @var PolicyInterface|MockObject $notExpectedToBeCalledPolicy */
        $notExpectedToBeCalledPolicy = $this->getMockByCalls(PolicyInterface::class);

        $policy = new AndPolicy([$compliantPolicy1, $nonCompliantPolicy, $notExpectedToBeCalledPolicy]);

        self::assertFalse($policy->isCompliantIncludingPath($path, $object, $context));

        $error = error_get_last();

        self::assertNotNull($error);

        self::assertSame(E_USER_DEPRECATED, $error['type']);
        self::assertSame(
            sprintf('The %s::isCompliant method is deprecated ().', PolicyInterface::class),
            $error['message']
        );
    }
}
