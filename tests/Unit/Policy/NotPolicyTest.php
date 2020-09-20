<?php

declare(strict_types=1);

namespace Chubbyphp\Tests\Deserialization\Unit\Policy;

use Chubbyphp\Deserialization\Denormalizer\DenormalizerContextInterface;
use Chubbyphp\Deserialization\Policy\NotPolicy;
use Chubbyphp\Deserialization\Policy\PolicyInterface;
use Chubbyphp\Mock\Call;
use Chubbyphp\Mock\MockByCallsTrait;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Chubbyphp\Deserialization\Policy\NotPolicy
 *
 * @internal
 */
final class NotPolicyTest extends TestCase
{
    use MockByCallsTrait;

    public function testIsCompliantIncludingPathReturnsTrueIfGivenPolicyIncludingPathReturnsFalse(): void
    {
        $object = new \stdClass();

        $path = '';

        /** @var DenormalizerContextInterface|MockObject $context */
        $context = $this->getMockByCalls(DenormalizerContextInterface::class, []);

        /** @var PolicyInterface|MockObject $nonCompliantPolicy */
        $nonCompliantPolicy = $this->getMockByCalls(PolicyInterface::class, [
            Call::create('isCompliant')->with($path, $object, $context)->willReturn(false),
        ]);

        $policy = new NotPolicy($nonCompliantPolicy);

        self::assertTrue($policy->isCompliant($path, $object, $context));
    }

    public function testIsCompliantIncludingPathReturnsFalseIfGivenPolicyIncludingPathReturnsTrue(): void
    {
        $object = new \stdClass();

        $path = '';

        /** @var DenormalizerContextInterface|MockObject $context */
        $context = $this->getMockByCalls(DenormalizerContextInterface::class, []);

        /** @var PolicyInterface|MockObject $nonCompliantPolicy */
        $nonCompliantPolicy = $this->getMockByCalls(PolicyInterface::class, [
            Call::create('isCompliant')->with($path, $object, $context)->willReturn(true),
        ]);

        $policy = new NotPolicy($nonCompliantPolicy);

        self::assertFalse($policy->isCompliant($path, $object, $context));
    }
}
