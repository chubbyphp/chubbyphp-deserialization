<?php

declare(strict_types=1);

namespace Chubbyphp\Tests\Deserialization\Unit\Decoder;

use Chubbyphp\Deserialization\Decoder\UrlEncodedTypeDecoder;
use Chubbyphp\Deserialization\DeserializerRuntimeException;

/**
 * @covers \Chubbyphp\Deserialization\Decoder\UrlEncodedTypeDecoder
 *
 * @internal
 */
final class UrlEncodedTypeDecoderTest extends AbstractTypeDecoderTest
{
    public function testGetContentType(): void
    {
        $decoder = new UrlEncodedTypeDecoder();

        self::assertSame('application/x-www-form-urlencoded', $decoder->getContentType());
    }

    /**
     * @dataProvider getExpectedData
     *
     * @param array $expectedData
     */
    public function testDecode(array $expectedData): void
    {
        $decoder = new UrlEncodedTypeDecoder();

        $urlEncoded = 'page=1&perPage=10&search=&sort=name&order=asc&_embedded[mainItem][id]=id1&_embedded[mainItem][name]=A+fancy+Name&_embedded[mainItem][treeValues][1][2]=3&_embedded[mainItem][progress]=76.8&_embedded[mainItem][active]=1&_embedded[mainItem][_type]=item&_embedded[mainItem][_links][read][href]=http%3A%2F%2Ftest.com%2Fitems%2Fid1&_embedded[mainItem][_links][read][method]=GET&_embedded[mainItem][_links][update][href]=http%3A%2F%2Ftest.com%2Fitems%2Fid1&_embedded[mainItem][_links][update][method]=PUT&_embedded[mainItem][_links][delete][href]=http%3A%2F%2Ftest.com%2Fitems%2Fid1&_embedded[mainItem][_links][delete][method]=DELETE&_embedded[items][0][id]=id1&_embedded[items][0][name]=A+fancy+Name&_embedded[items][0][treeValues][1][2]=3&_embedded[items][0][progress]=76.8&_embedded[items][0][active]=1&_embedded[items][0][_type]=item&_embedded[items][0][_links][read][href]=http%3A%2F%2Ftest.com%2Fitems%2Fid1&_embedded[items][0][_links][read][method]=GET&_embedded[items][0][_links][update][href]=http%3A%2F%2Ftest.com%2Fitems%2Fid1&_embedded[items][0][_links][update][method]=PUT&_embedded[items][0][_links][delete][href]=http%3A%2F%2Ftest.com%2Fitems%2Fid1&_embedded[items][0][_links][delete][method]=DELETE&_embedded[items][1][id]=id2&_embedded[items][1][name]=B+fancy+Name&_embedded[items][1][treeValues][1][2]=3&_embedded[items][1][treeValues][1][3]=4&_embedded[items][1][progress]=24.7&_embedded[items][1][active]=1&_embedded[items][1][_type]=item&_embedded[items][1][_links][read][href]=http%3A%2F%2Ftest.com%2Fitems%2Fid2&_embedded[items][1][_links][read][method]=GET&_embedded[items][1][_links][update][href]=http%3A%2F%2Ftest.com%2Fitems%2Fid2&_embedded[items][1][_links][update][method]=PUT&_embedded[items][1][_links][delete][href]=http%3A%2F%2Ftest.com%2Fitems%2Fid2&_embedded[items][1][_links][delete][method]=DELETE&_embedded[items][2][id]=id3&_embedded[items][2][name]=C+fancy+Name&_embedded[items][2][treeValues][1][2]=3&_embedded[items][2][treeValues][1][3]=4&_embedded[items][2][treeValues][1][6]=7&_embedded[items][2][progress]=100&_embedded[items][2][active]=&_embedded[items][2][_type]=item&_embedded[items][2][_links][read][href]=http%3A%2F%2Ftest.com%2Fitems%2Fid3&_embedded[items][2][_links][read][method]=GET&_embedded[items][2][_links][update][href]=http%3A%2F%2Ftest.com%2Fitems%2Fid3&_embedded[items][2][_links][update][method]=PUT&_embedded[items][2][_links][delete][href]=http%3A%2F%2Ftest.com%2Fitems%2Fid3&_embedded[items][2][_links][delete][method]=DELETE&_links[self][href]=http%3A%2F%2Ftest.com%2Fitems%2F%3Fpage%3D1%26perPage%3D10%26sort%3Dname%26order%3Dasc&_links[self][method]=GET&_links[create][href]=http%3A%2F%2Ftest.com%2Fitems%2F&_links[create][method]=POST&_type=search';

        self::assertEquals($expectedData, $decoder->decode($urlEncoded));
    }

    public function testTypes(): void
    {
        $urlEncoded = 'id=id1&name=A+fancy+Name&treeValues[1][2]=3&progress=76.8&active=true&inactive=false&phone=0041000000000';

        $decoder = new UrlEncodedTypeDecoder();

        $data = $decoder->decode($urlEncoded);

        self::assertSame('id1', $data['id']);
        self::assertSame('A fancy Name', $data['name']);
        self::assertSame([1 => [2 => 3]], $data['treeValues']);
        self::assertSame(76.8, $data['progress']);
        self::assertSame(true, $data['active']);
        self::assertSame(false, $data['inactive']);
        self::assertSame('0041000000000', $data['phone']);
    }

    public function testInvalidDecode(): void
    {
        $this->expectException(DeserializerRuntimeException::class);
        $this->expectExceptionMessage('Data is not parsable with content-type: "application/x-www-form-urlencoded"');
        $decoderType = new UrlEncodedTypeDecoder();
        $decoderType->decode('====');
    }
}
