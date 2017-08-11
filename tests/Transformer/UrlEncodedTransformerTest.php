<?php

declare(strict_types=1);

namespace Chubbyphp\Tests\Deserialization\Transformer;

use Chubbyphp\Deserialization\Transformer\TransformerException;
use Chubbyphp\Deserialization\Transformer\UrlEncodedTransformer;

/**
 * @covers \Chubbyphp\Deserialization\Transformer\UrlEncodedTransformer
 */
class UrlEncodedTransformerTest extends AbstractTransformerTest
{
    public function testContentType()
    {
        $transformer = new UrlEncodedTransformer();

        self::assertSame('application/x-www-form-urlencoded', $transformer->getContentType());
    }

    /**
     * @dataProvider dataProvider
     *
     * @param array $expectedData
     */
    public function testTransform(array $expectedData)
    {
        $transformer = new UrlEncodedTransformer();

        $urlEncoded = 'page=1&perPage=10&search=&sort=name&order=asc&_embedded[mainItem][id]=id1&_embedded[mainItem][name]=A+fancy+Name&_embedded[mainItem][treeValues][1][2]=3&_embedded[mainItem][progress]=76.8&_embedded[mainItem][active]=1&_embedded[mainItem][_type]=item&_embedded[mainItem][_links][read][href]=http%3A%2F%2Ftest.com%2Fitems%2Fid1&_embedded[mainItem][_links][read][method]=GET&_embedded[mainItem][_links][update][href]=http%3A%2F%2Ftest.com%2Fitems%2Fid1&_embedded[mainItem][_links][update][method]=PUT&_embedded[mainItem][_links][delete][href]=http%3A%2F%2Ftest.com%2Fitems%2Fid1&_embedded[mainItem][_links][delete][method]=DELETE&_embedded[items][0][id]=id1&_embedded[items][0][name]=A+fancy+Name&_embedded[items][0][treeValues][1][2]=3&_embedded[items][0][progress]=76.8&_embedded[items][0][active]=1&_embedded[items][0][_type]=item&_embedded[items][0][_links][read][href]=http%3A%2F%2Ftest.com%2Fitems%2Fid1&_embedded[items][0][_links][read][method]=GET&_embedded[items][0][_links][update][href]=http%3A%2F%2Ftest.com%2Fitems%2Fid1&_embedded[items][0][_links][update][method]=PUT&_embedded[items][0][_links][delete][href]=http%3A%2F%2Ftest.com%2Fitems%2Fid1&_embedded[items][0][_links][delete][method]=DELETE&_embedded[items][1][id]=id2&_embedded[items][1][name]=B+fancy+Name&_embedded[items][1][treeValues][1][2]=3&_embedded[items][1][treeValues][1][3]=4&_embedded[items][1][progress]=24.7&_embedded[items][1][active]=1&_embedded[items][1][_type]=item&_embedded[items][1][_links][read][href]=http%3A%2F%2Ftest.com%2Fitems%2Fid2&_embedded[items][1][_links][read][method]=GET&_embedded[items][1][_links][update][href]=http%3A%2F%2Ftest.com%2Fitems%2Fid2&_embedded[items][1][_links][update][method]=PUT&_embedded[items][1][_links][delete][href]=http%3A%2F%2Ftest.com%2Fitems%2Fid2&_embedded[items][1][_links][delete][method]=DELETE&_embedded[items][2][id]=id3&_embedded[items][2][name]=C+fancy+Name&_embedded[items][2][treeValues][1][2]=3&_embedded[items][2][treeValues][1][3]=4&_embedded[items][2][treeValues][1][6]=7&_embedded[items][2][progress]=100&_embedded[items][2][active]=&_embedded[items][2][_type]=item&_embedded[items][2][_links][read][href]=http%3A%2F%2Ftest.com%2Fitems%2Fid3&_embedded[items][2][_links][read][method]=GET&_embedded[items][2][_links][update][href]=http%3A%2F%2Ftest.com%2Fitems%2Fid3&_embedded[items][2][_links][update][method]=PUT&_embedded[items][2][_links][delete][href]=http%3A%2F%2Ftest.com%2Fitems%2Fid3&_embedded[items][2][_links][delete][method]=DELETE&_links[self][href]=http%3A%2F%2Ftest.com%2Fitems%2F%3Fpage%3D1%26perPage%3D10%26sort%3Dname%26order%3Dasc&_links[self][method]=GET&_links[create][href]=http%3A%2F%2Ftest.com%2Fitems%2F&_links[create][method]=POST&_type=search';

        $data = $transformer->transform($urlEncoded);

        self::assertEquals($expectedData, $data);
    }

    public function testWithNumericPrefix()
    {
        $transformer = new UrlEncodedTransformer('prefix');

        $urlEncoded = 'key[prefix0]=value1&key[prefix1]=value2';

        $data = $transformer->transform($urlEncoded);

        self::assertEquals(['key' => ['value1', 'value2']], $data);
    }

    public function testInvalidTransform()
    {
        self::expectException(TransformerException::class);
        self::expectExceptionMessage('Transform error: UrlEncoded not parsable');

        $transformer = new UrlEncodedTransformer();
        $transformer->transform('====');
    }
}
