<?php

declare(strict_types=1);

namespace Chubbyphp\Tests\Deserialization\Unit\Policy;

use Chubbyphp\Deserialization\Denormalizer\DenormalizerContextInterface;
use Chubbyphp\Deserialization\Policy\GroupPolicy;
use Chubbyphp\Mock\Call;
use Chubbyphp\Mock\MockByCallsTrait;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Chubbyphp\Deserialization\Policy\GroupPolicy
 *
 * @internal
 */
final class GroupPolicyTest extends TestCase
{
    use MockByCallsTrait;

    public function testIsCompliantIncludingPathReturnsTrueIfNoGroupsAreSet(): void
    {
        $object = new \stdClass();

        $path = '';

        /** @var DenormalizerContextInterface|MockObject $context */
        $context = $this->getMockByCalls(DenormalizerContextInterface::class);

        $policy = new GroupPolicy([]);

        self::assertTrue($policy->isCompliantIncludingPath($path, $object, $context));
    }

    public function testIsCompliantIncludingPathReturnsTrueWithDefaultValues(): void
    {
        $object = new \stdClass();

        $path = '';

        /** @var DenormalizerContextInterface|MockObject $context */
        $context = $this->getMockByCalls(DenormalizerContextInterface::class, [
            Call::create('getAttribute')
                ->with(GroupPolicy::ATTRIBUTE_GROUPS, [GroupPolicy::GROUP_DEFAULT])
                ->willReturn([GroupPolicy::GROUP_DEFAULT]),
        ]);

        $policy = new GroupPolicy();

        self::assertTrue($policy->isCompliantIncludingPath($path, $object, $context));
    }

    public function testIsCompliantIncludingPathReturnsTrueIfOneGroupMatches(): void
    {
        $object = new \stdClass();

        $path = '';

        /** @var DenormalizerContextInterface|MockObject $context */
        $context = $this->getMockByCalls(DenormalizerContextInterface::class, [
            Call::create('getAttribute')
                ->with(GroupPolicy::ATTRIBUTE_GROUPS, [GroupPolicy::GROUP_DEFAULT])
                ->willReturn(['group2']),
        ]);

        $policy = new GroupPolicy(['group1', 'group2']);

        self::assertTrue($policy->isCompliantIncludingPath($path, $object, $context));
    }

    public function testIsCompliantIncludingPathReturnsFalseIfNoGroupsAreSetInContext(): void
    {
        $object = new \stdClass();

        $path = '';

        /** @var DenormalizerContextInterface|MockObject $context */
        $context = $this->getMockByCalls(DenormalizerContextInterface::class, [
            Call::create('getAttribute')
                ->with(GroupPolicy::ATTRIBUTE_GROUPS, [GroupPolicy::GROUP_DEFAULT])
                ->willReturn([]),
        ]);

        $policy = new GroupPolicy(['group1', 'group2']);

        self::assertFalse($policy->isCompliantIncludingPath($path, $object, $context));
    }

    public function testIsCompliantIncludingPathReturnsFalseIfNoGroupsMatch(): void
    {
        $object = new \stdClass();

        $path = '';

        /** @var DenormalizerContextInterface|MockObject $context */
        $context = $this->getMockByCalls(DenormalizerContextInterface::class, [
            Call::create('getAttribute')
                ->with(GroupPolicy::ATTRIBUTE_GROUPS, [GroupPolicy::GROUP_DEFAULT])
                ->willReturn(['unknownGroup']),
        ]);

        $policy = new GroupPolicy(['group1', 'group2']);

        self::assertFalse($policy->isCompliantIncludingPath($path, $object, $context));
    }
}
