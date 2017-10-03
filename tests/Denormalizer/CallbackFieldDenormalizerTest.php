<?php

declare(strict_types=1);

namespace Chubbyphp\Tests\Deserialization\Denormalizer;

use Chubbyphp\Deserialization\Denormalizer\DenormalizerContextInterface;
use Chubbyphp\Deserialization\Denormalizer\CallbackFieldDenormalizer;
use Chubbyphp\Deserialization\Denormalizer\DenormalizerInterface;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Chubbyphp\Deserialization\Denormalizer\CallbackFieldDenormalizer
 */
class CallbackFieldDenormalizerTest extends TestCase
{
    public function testDenormalizeField()
    {
        $object = new class() {
            /**
             * @var string
             */
            private $name;

            /**
             * @return string
             */
            public function getName(): string
            {
                return $this->name;
            }

            /**
             * @param string $name
             *
             * @return self
             */
            public function setName(string $name): self
            {
                $this->name = $name;

                return $this;
            }
        };

        $fieldDenormalizer = new CallbackFieldDenormalizer(
            function (
                string $path,
                $object,
                $value,
                DenormalizerContextInterface $context,
                DenormalizerInterface $denormalizer = null
            ) {
                $object->setName($value);
            }
        );
        $fieldDenormalizer->denormalizeField('name', $object, 'name', $this->getDenormalizerContext());

        self::assertSame('name', $object->getName());
    }

    /**
     * @return DenormalizerContextInterface
     */
    private function getDenormalizerContext(): DenormalizerContextInterface
    {
        /** @var DenormalizerContextInterface|\PHPUnit_Framework_MockObject_MockObject $context */
        $context = $this->getMockBuilder(DenormalizerContextInterface::class)->getMockForAbstractClass();

        return $context;
    }
}
