<?php

declare(strict_types=1);

namespace Chubbyphp\Tests\Deserialization\Unit\Denormalizer;

use Chubbyphp\Deserialization\Accessor\AccessorInterface;
use Chubbyphp\Deserialization\Denormalizer\DenormalizerContextInterface;
use Chubbyphp\Deserialization\Denormalizer\FieldDenormalizer;
use Chubbyphp\Mock\Call;
use Chubbyphp\Mock\MockByCallsTrait;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Chubbyphp\Deserialization\Denormalizer\FieldDenormalizer
 *
 * @internal
 */
final class FieldDenormalizerTest extends TestCase
{
    use MockByCallsTrait;

    public function testDenormalizeField(): void
    {
        $object = new \stdClass();

        /** @var AccessorInterface|MockObject $accessor */
        $accessor = $this->getMockByCalls(AccessorInterface::class, [
            Call::create('setValue')->with($object, 'name'),
        ]);

        /** @var DenormalizerContextInterface|MockObject $context */
        $context = $this->getMockByCalls(DenormalizerContextInterface::class);

        $fieldDenormalizer = new FieldDenormalizer($accessor);
        $fieldDenormalizer->denormalizeField('name', $object, 'name', $context);
    }

    public function testDenormalizeFieldWithEmptyToNullDisabled(): void
    {
        $object = new \stdClass();

        /** @var AccessorInterface|MockObject $accessor */
        $accessor = $this->getMockByCalls(AccessorInterface::class, [
            Call::create('setValue')->with($object, ''),
        ]);

        /** @var DenormalizerContextInterface|MockObject $context */
        $context = $this->getMockByCalls(DenormalizerContextInterface::class);

        $fieldDenormalizer = new FieldDenormalizer($accessor);
        $fieldDenormalizer->denormalizeField('name', $object, '', $context);
    }

    public function testDenormalizeFieldWithEmptyToNullEnabled(): void
    {
        $object = new \stdClass();

        /** @var AccessorInterface|MockObject $accessor */
        $accessor = $this->getMockByCalls(AccessorInterface::class, [
            Call::create('setValue')->with($object, null),
        ]);

        /** @var DenormalizerContextInterface|MockObject $context */
        $context = $this->getMockByCalls(DenormalizerContextInterface::class);

        $fieldDenormalizer = new FieldDenormalizer($accessor, true);
        $fieldDenormalizer->denormalizeField('name', $object, '', $context);
    }
}
