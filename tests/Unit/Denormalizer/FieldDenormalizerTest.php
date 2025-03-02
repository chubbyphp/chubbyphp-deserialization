<?php

declare(strict_types=1);

namespace Chubbyphp\Tests\Deserialization\Unit\Denormalizer;

use Chubbyphp\Deserialization\Accessor\AccessorInterface;
use Chubbyphp\Deserialization\Denormalizer\DenormalizerContextInterface;
use Chubbyphp\Deserialization\Denormalizer\FieldDenormalizer;
use Chubbyphp\Mock\MockMethod\WithReturn;
use Chubbyphp\Mock\MockObjectBuilder;
use PHPUnit\Framework\Attributes\DoesNotPerformAssertions;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Chubbyphp\Deserialization\Denormalizer\FieldDenormalizer
 *
 * @internal
 */
final class FieldDenormalizerTest extends TestCase
{
    #[DoesNotPerformAssertions]
    public function testDenormalizeField(): void
    {
        $object = new \stdClass();

        $builder = new MockObjectBuilder();

        /** @var AccessorInterface $accessor */
        $accessor = $builder->create(AccessorInterface::class, [
            new WithReturn('setValue', [$object, 'name'], null),
        ]);

        /** @var DenormalizerContextInterface $context */
        $context = $builder->create(DenormalizerContextInterface::class, []);

        $fieldDenormalizer = new FieldDenormalizer($accessor);
        $fieldDenormalizer->denormalizeField('name', $object, 'name', $context);
    }

    #[DoesNotPerformAssertions]
    public function testDenormalizeFieldWithEmptyToNullDisabled(): void
    {
        $object = new \stdClass();

        $builder = new MockObjectBuilder();

        /** @var AccessorInterface $accessor */
        $accessor = $builder->create(AccessorInterface::class, [
            new WithReturn('setValue', [$object, ''], null),
        ]);

        $context = $builder->create(DenormalizerContextInterface::class, []);

        $fieldDenormalizer = new FieldDenormalizer($accessor);
        $fieldDenormalizer->denormalizeField('name', $object, '', $context);
    }

    #[DoesNotPerformAssertions]
    public function testDenormalizeFieldWithEmptyToNullEnabled(): void
    {
        $object = new \stdClass();

        $builder = new MockObjectBuilder();

        /** @var AccessorInterface $accessor */
        $accessor = $builder->create(AccessorInterface::class, [
            new WithReturn('setValue', [$object, null], null),
        ]);

        $context = $builder->create(DenormalizerContextInterface::class, []);

        $fieldDenormalizer = new FieldDenormalizer($accessor, true);
        $fieldDenormalizer->denormalizeField('name', $object, '', $context);
    }
}
