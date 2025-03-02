<?php

declare(strict_types=1);

namespace Chubbyphp\Tests\Deserialization\Unit\Policy;

use Chubbyphp\Deserialization\Denormalizer\DenormalizerContextInterface;
use Chubbyphp\Deserialization\Policy\CallbackPolicy;
use Chubbyphp\Mock\MockObjectBuilder;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Chubbyphp\Deserialization\Policy\CallbackPolicy
 *
 * @internal
 */
final class CallbackPolicyTest extends TestCase
{
    public function testIsCompliantIncludingPathReturnsTrueIfCallbackReturnsTrue(): void
    {
        $object = new \stdClass();

        $path = '';

        $builder = new MockObjectBuilder();

        /** @var DenormalizerContextInterface $context */
        $context = $builder->create(DenormalizerContextInterface::class, []);

        $policy = new CallbackPolicy(
            static function ($pathParameter, $objectParameter, $contextParameter) use ($path, $object, $context) {
                self::assertSame($context, $contextParameter);
                self::assertSame($object, $objectParameter);
                self::assertSame($path, $pathParameter);

                return true;
            }
        );

        self::assertTrue($policy->isCompliant($path, $object, $context));
    }

    public function testIsCompliantIncludingPathReturnsFalseIfCallbackReturnsFalse(): void
    {
        $object = new \stdClass();

        $path = '';

        $builder = new MockObjectBuilder();

        /** @var DenormalizerContextInterface $context */
        $context = $builder->create(DenormalizerContextInterface::class, []);

        $policy = new CallbackPolicy(
            static function ($pathParameter, $objectParameter, $contextParameter) use ($path, $object, $context) {
                self::assertSame($context, $contextParameter);
                self::assertSame($object, $objectParameter);
                self::assertSame($path, $pathParameter);

                return false;
            }
        );

        self::assertFalse($policy->isCompliant($path, $object, $context));
    }
}
