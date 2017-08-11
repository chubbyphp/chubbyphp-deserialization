<?php

declare(strict_types=1);

namespace Chubbyphp\Tests\Deserialization\Transformer;

use Chubbyphp\Deserialization\Transformer\YamlTransformer;

/**
 * @covers \Chubbyphp\Deserialization\Transformer\YamlTransformer
 */
class YamlTransformerTest extends AbstractTransformerTest
{
    public function testContentType()
    {
        $transformer = new YamlTransformer();

        self::assertSame('application/x-yaml', $transformer->getContentType());
    }

    /**
     * @dataProvider dataProvider
     *
     * @param array $expectedData
     */
    public function testTransform(array $expectedData)
    {
        $transformer = new YamlTransformer();

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

        $data = $transformer->transform($yaml);

        self::assertEquals($expectedData, $data);
    }
}
