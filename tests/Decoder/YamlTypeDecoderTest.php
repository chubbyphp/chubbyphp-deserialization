<?php

declare(strict_types=1);

namespace Chubbyphp\Tests\Deserialization\Decoder;

use Chubbyphp\Deserialization\Decoder\YamlTypeDecoder;
use Chubbyphp\Deserialization\DeserializerRuntimeException;

/**
 * @covers \Chubbyphp\Deserialization\Decoder\YamlTypeDecoder
 */
class YamlTypeDecoderTest extends AbstractTypeDecoderTest
{
    public function testGetContentType()
    {
        $decoder = new YamlTypeDecoder();

        self::assertSame('application/x-yaml', $decoder->getContentType());
    }

    /**
     * @dataProvider getExpectedData
     *
     * @param array $expectedData
     */
    public function testDecode(array $expectedData)
    {
        $yaml = <<<EOD
page: 1
perPage: 10
search: null
sort: name
order: asc
_embedded:
    mainItem:
        id: id1
        name: 'A fancy Name'
        treeValues:
            1:
                2: 3
        progress: 76.8
        active: true
        _type: item
        _links:
            read:
                href: 'http://test.com/items/id1'
                method: GET
            update:
                href: 'http://test.com/items/id1'
                method: PUT
            delete:
                href: 'http://test.com/items/id1'
                method: DELETE
    items:
        -
            id: id1
            name: 'A fancy Name'
            treeValues:
                1:
                    2: 3
            progress: 76.8
            active: true
            _type: item
            _links:
                read:
                    href: 'http://test.com/items/id1'
                    method: GET
                update:
                    href: 'http://test.com/items/id1'
                    method: PUT
                delete:
                    href: 'http://test.com/items/id1'
                    method: DELETE
        -
            id: id2
            name: 'B fancy Name'
            treeValues:
                1:
                    2: 3
                    3: 4
            progress: 24.7
            active: true
            _type: item
            _links:
                read:
                    href: 'http://test.com/items/id2'
                    method: GET
                update:
                    href: 'http://test.com/items/id2'
                    method: PUT
                delete:
                    href: 'http://test.com/items/id2'
                    method: DELETE
        -
            id: id3
            name: 'C fancy Name'
            treeValues:
                1:
                    2: 3
                    3: 4
                    6: 7
            progress: !!float 100
            active: false
            _type: item
            _links:
                read:
                    href: 'http://test.com/items/id3'
                    method: GET
                update:
                    href: 'http://test.com/items/id3'
                    method: PUT
                delete:
                    href: 'http://test.com/items/id3'
                    method: DELETE
_links:
    self:
        href: 'http://test.com/items/?page=1&perPage=10&sort=name&order=asc'
        method: GET
    create:
        href: 'http://test.com/items/'
        method: POST
_type: search
EOD;

        $decoder = new YamlTypeDecoder();

        self::assertEquals($expectedData, $decoder->decode($yaml));
    }

    public function testTypes()
    {
        $yaml = <<<EOD
id: id1
name: 'A fancy Name'
treeValues:
    1:
        2: 3
progress: 76.8
active: true
inactive: false
phone: '0041000000000'
EOD;

        $decoder = new YamlTypeDecoder();

        $data = $decoder->decode($yaml);

        self::assertSame('id1', $data['id']);
        self::assertSame('A fancy Name', $data['name']);
        self::assertSame([1 => [2 => 3]], $data['treeValues']);
        self::assertSame(76.8, $data['progress']);
        self::assertSame(true, $data['active']);
        self::assertSame(false, $data['inactive']);
        self::assertSame('0041000000000', $data['phone']);
    }

    public function testInvalidDecode()
    {
        self::expectException(DeserializerRuntimeException::class);
        self::expectExceptionMessage('Data is not parsable with content-type: "application/x-yaml"');
        $decoderType = new YamlTypeDecoder();
        $decoderType->decode("\ttest");
    }

    public function testNotArrayDecode()
    {
        self::expectException(DeserializerRuntimeException::class);
        self::expectExceptionMessage('Data is not parsable with content-type: "application/x-yaml"');
        $decoderType = new YamlTypeDecoder();
        $decoderType->decode('null');
    }
}
