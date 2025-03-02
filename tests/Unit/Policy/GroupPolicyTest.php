<?php

declare(strict_types=1);

namespace Chubbyphp\Tests\Deserialization\Unit\Policy;

use Chubbyphp\Deserialization\Denormalizer\DenormalizerContextInterface;
use Chubbyphp\Deserialization\Policy\GroupPolicy;
use Chubbyphp\Mock\MockMethod\WithReturn;
use Chubbyphp\Mock\MockObjectBuilder;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Chubbyphp\Deserialization\Policy\GroupPolicy
 *
 * @internal
 */
final class GroupPolicyTest extends TestCase
{
    public function testIsCompliantIncludingPathReturnsTrueIfNoGroupsAreSet(): void
    {
        $object = new \stdClass();

        $path = '';

        $builder = new MockObjectBuilder();

        /** @var DenormalizerContextInterface $context */
        $context = $builder->create(DenormalizerContextInterface::class, []);

        $policy = new GroupPolicy([]);

        self::assertTrue($policy->isCompliant($path, $object, $context));
    }

    public function testIsCompliantIncludingPathReturnsTrueWithDefaultValues(): void
    {
        $object = new \stdClass();

        $path = '';

        $builder = new MockObjectBuilder();

        /** @var DenormalizerContextInterface $context */
        $context = $builder->create(DenormalizerContextInterface::class, [
            new WithReturn(
                'getAttribute',
                [GroupPolicy::ATTRIBUTE_GROUPS, [GroupPolicy::GROUP_DEFAULT]],
                [GroupPolicy::GROUP_DEFAULT]
            ),
        ]);

        $policy = new GroupPolicy();

        self::assertTrue($policy->isCompliant($path, $object, $context));
    }

    public function testIsCompliantIncludingPathReturnsTrueIfOneGroupMatches(): void
    {
        $object = new \stdClass();

        $path = '';

        $builder = new MockObjectBuilder();

        /** @var DenormalizerContextInterface $context */
        $context = $builder->create(DenormalizerContextInterface::class, [
            new WithReturn('getAttribute', [GroupPolicy::ATTRIBUTE_GROUPS, [GroupPolicy::GROUP_DEFAULT]], ['group2']),
        ]);

        $policy = new GroupPolicy(['group1', 'group2']);

        self::assertTrue($policy->isCompliant($path, $object, $context));
    }

    public function testIsCompliantIncludingPathReturnsFalseIfNoGroupsAreSetInContext(): void
    {
        $object = new \stdClass();

        $path = '';

        $builder = new MockObjectBuilder();

        /** @var DenormalizerContextInterface $context */
        $context = $builder->create(DenormalizerContextInterface::class, [
            new WithReturn('getAttribute', [GroupPolicy::ATTRIBUTE_GROUPS, [GroupPolicy::GROUP_DEFAULT]], []),
        ]);

        $policy = new GroupPolicy(['group1', 'group2']);

        self::assertFalse($policy->isCompliant($path, $object, $context));
    }

    public function testIsCompliantIncludingPathReturnsFalseIfNoGroupsMatch(): void
    {
        $object = new \stdClass();

        $path = '';

        $builder = new MockObjectBuilder();

        /** @var DenormalizerContextInterface $context */
        $context = $builder->create(DenormalizerContextInterface::class, [
            new WithReturn(
                'getAttribute',
                [GroupPolicy::ATTRIBUTE_GROUPS, [GroupPolicy::GROUP_DEFAULT]],
                ['unknownGroup']
            ),
        ]);

        $policy = new GroupPolicy(['group1', 'group2']);

        self::assertFalse($policy->isCompliant($path, $object, $context));
    }
}
