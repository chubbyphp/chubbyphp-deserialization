<?php

declare(strict_types=1);

namespace Chubbyphp\Tests\Deserialization;

use Chubbyphp\Deserialization\Transformer\TransformerInterface as ContenTypeTransformerInterface;
use Chubbyphp\Deserialization\Transformer;

/**
 * @covers \Chubbyphp\Deserialization\Transformer
 */
final class TransformerTest extends \PHPUnit_Framework_TestCase
{
    public function testWithKnownTransformers()
    {
        $mappings = [
            $this->getJsonTransformer(),
            $this->getUrlEncodedTransformer(),
        ];

        $registry = new Transformer($mappings);

        self::assertEquals(['key' => 'value'], $registry->transform('{"key":"value"}', 'application/json'));
        self::assertEquals(['key' => 'value'], $registry->transform('key=value', 'application/x-www-form-urlencoded'));
    }

    public function testWithUnknownTransformers()
    {
        self::expectException(\InvalidArgumentException::class);
        self::expectExceptionMessage('There is no transformer for content-type: application/x-yaml');

        $registry = new Transformer([]);

        $registry->transform('key: value', 'application/x-yaml');
    }

    /**
     * @return ContenTypeTransformerInterface
     */
    private function getJsonTransformer(): ContenTypeTransformerInterface
    {
        /** @var ContenTypeTransformerInterface|\PHPUnit_Framework_MockObject_MockObject $mapping */
        $mapping = $this->getMockBuilder(ContenTypeTransformerInterface::class)->getMockForAbstractClass();

        $mapping->expects(self::any())->method('getContentType')->willReturn('application/json');
        $mapping->expects(self::any())->method('transform')->willReturnCallback(
            function (string $string) {
                return json_decode($string, true);
            }
        );

        return $mapping;
    }

    /**
     * @return ContenTypeTransformerInterface
     */
    private function getUrlEncodedTransformer(): ContenTypeTransformerInterface
    {
        /** @var ContenTypeTransformerInterface|\PHPUnit_Framework_MockObject_MockObject $mapping */
        $mapping = $this->getMockBuilder(ContenTypeTransformerInterface::class)->getMockForAbstractClass();

        $mapping->expects(self::any())->method('getContentType')->willReturn('application/x-www-form-urlencoded');
        $mapping->expects(self::any())->method('transform')->willReturnCallback(
            function (string $string) {
                $data = [];
                parse_str($string, $data);

                return $data;
            }
        );

        return $mapping;
    }
}
