<?php

declare(strict_types=1);

namespace Chubbyphp\Tests\Deserialization\Unit\Policy;

use Chubbyphp\Deserialization\Denormalizer\DenormalizerContextInterface;
use Chubbyphp\Deserialization\Policy\GroupPolicy;
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

    public function testIsCompliantReturnsTrueIfNoGroupsAreSet(): void
    {
        $object = new \stdClass();

        /** @var DenormalizerContextInterface|MockObject $context */
        $context = $this->getMockByCalls(DenormalizerContextInterface::class);

        $policy = new GroupPolicy([]);

        self::assertTrue($policy->isCompliant($context, $object));
    }

    public function testIsCompliantReturnsTrueWithDefaultValues(): void
    {
        $object = new \stdClass();

        /** @var DenormalizerContextInterface|MockObject $context */
        $context = $this->getDenormalizerContextWithGroupAttribute(null);

        $policy = new GroupPolicy();

        self::assertTrue($policy->isCompliant($context, $object));
    }

    public function testIsCompliantReturnsTrueIfOneGroupMatches(): void
    {
        $object = new \stdClass();

        /** @var DenormalizerContextInterface|MockObject $context */
        $context = $this->getDenormalizerContextWithGroupAttribute(['group2']);

        $policy = new GroupPolicy(['group1', 'group2']);

        self::assertTrue($policy->isCompliant($context, $object));
    }

    public function testIsCompliantReturnsFalseIfNoGroupsAreSetInContext(): void
    {
        $object = new \stdClass();

        /** @var DenormalizerContextInterface|MockObject $context */
        $context = $this->getDenormalizerContextWithGroupAttribute([]);

        $policy = new GroupPolicy(['group1', 'group2']);

        self::assertFalse($policy->isCompliant($context, $object));
    }

    public function testIsCompliantReturnsFalseIfNoGroupsMatch(): void
    {
        $object = new \stdClass();

        /** @var DenormalizerContextInterface|MockObject $context */
        $context = $this->getDenormalizerContextWithGroupAttribute(['unknownGroup']);

        $policy = new GroupPolicy(['group1', 'group2']);

        self::assertFalse($policy->isCompliant($context, $object));
    }

    private function getDenormalizerContextWithGroupAttribute(array $groups = null): DenormalizerContextInterface
    {
        return new class($groups) implements DenormalizerContextInterface {
            private $groups;

            public function __construct($groups)
            {
                $this->groups = $groups;
            }

            public function getAllowedAdditionalFields()
            {
                return null;
            }

            public function getGroups(): array
            {
                return [];
            }

            public function getRequest()
            {
                return null;
            }

            public function getAttributes(): array
            {
                return [];
            }

            public function getAttribute(string $name, $default = null)
            {
                return $this->groups ?? $default;
            }

            public function withAttribute(string $name, $value): DenormalizerContextInterface
            {
                return $this;
            }
        };
    }
}
