<?php

declare(strict_types=1);

namespace Chubbyphp\Tests\Deserialization\Decoder;

use Chubbyphp\Deserialization\Decoder\JsonxTypeDecoder;
use Chubbyphp\Deserialization\DeserializerRuntimeException;

/**
 * @covers \Chubbyphp\Deserialization\Decoder\JsonxTypeDecoder
 */
class JsonxTypeDecoderTest extends AbstractTypeDecoderTest
{
    public function testGetContentType()
    {
        $decoder = new JsonxTypeDecoder();

        self::assertSame('application/x-jsonx', $decoder->getContentType());
    }

    /**
     * @dataProvider getExpectedData
     *
     * @param array $expectedData
     */
    public function testDecode(array $expectedData)
    {
        $jsonx = <<<EOD
<?xml version="1.0" encoding="UTF-8"?>
<json:object xsi:schemaLocation="http://www.datapower.com/schemas/json jsonx.xsd" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:json="http://www.ibm.com/xmlns/prod/2009/jsonx">
  <json:number name="page">1</json:number>
  <json:number name="perPage">10</json:number>
  <json:null name="search"/>
  <json:string name="sort">name</json:string>
  <json:string name="order">asc</json:string>
  <json:object name="_embedded">
    <json:object name="mainItem">
      <json:string name="id">id1</json:string>
      <json:string name="name">A fancy Name</json:string>
      <json:object name="treeValues">
        <json:object name="1">
          <json:number name="2">3</json:number>
        </json:object>
      </json:object>
      <json:number name="progress">76.8</json:number>
      <json:boolean name="active">true</json:boolean>
      <json:string name="_type">item</json:string>
      <json:object name="_links">
        <json:object name="read">
          <json:string name="href">http://test.com/items/id1</json:string>
          <json:string name="method">GET</json:string>
        </json:object>
        <json:object name="update">
          <json:string name="href">http://test.com/items/id1</json:string>
          <json:string name="method">PUT</json:string>
        </json:object>
        <json:object name="delete">
          <json:string name="href">http://test.com/items/id1</json:string>
          <json:string name="method">DELETE</json:string>
        </json:object>
      </json:object>
    </json:object>
    <json:array name="items">
      <json:object>
        <json:string name="id">id1</json:string>
        <json:string name="name">A fancy Name</json:string>
        <json:object name="treeValues">
          <json:object name="1">
            <json:number name="2">3</json:number>
          </json:object>
        </json:object>
        <json:number name="progress">76.8</json:number>
        <json:boolean name="active">true</json:boolean>
        <json:string name="_type">item</json:string>
        <json:object name="_links">
          <json:object name="read">
            <json:string name="href">http://test.com/items/id1</json:string>
            <json:string name="method">GET</json:string>
          </json:object>
          <json:object name="update">
            <json:string name="href">http://test.com/items/id1</json:string>
            <json:string name="method">PUT</json:string>
          </json:object>
          <json:object name="delete">
            <json:string name="href">http://test.com/items/id1</json:string>
            <json:string name="method">DELETE</json:string>
          </json:object>
        </json:object>
      </json:object>
      <json:object>
        <json:string name="id">id2</json:string>
        <json:string name="name">B fancy Name</json:string>
        <json:object name="treeValues">
          <json:object name="1">
            <json:number name="2">3</json:number>
            <json:number name="3">4</json:number>
          </json:object>
        </json:object>
        <json:number name="progress">24.7</json:number>
        <json:boolean name="active">true</json:boolean>
        <json:string name="_type">item</json:string>
        <json:object name="_links">
        <json:object name="read">
          <json:string name="href">http://test.com/items/id2</json:string>
          <json:string name="method">GET</json:string>
        </json:object>
        <json:object name="update">
          <json:string name="href">http://test.com/items/id2</json:string>
          <json:string name="method">PUT</json:string>
        </json:object>
        <json:object name="delete">
          <json:string name="href">http://test.com/items/id2</json:string>
          <json:string name="method">DELETE</json:string>
        </json:object>
      </json:object>
      </json:object>
      <json:object>
        <json:string name="id">id3</json:string>
        <json:string name="name">C fancy Name</json:string>
        <json:object name="treeValues">
          <json:object name="1">
            <json:number name="2">3</json:number>
            <json:number name="3">4</json:number>
            <json:number name="6">7</json:number>
          </json:object>
        </json:object>
        <json:number name="progress">100</json:number>
        <json:boolean name="active">false</json:boolean>
        <json:string name="_type">item</json:string>
        <json:object name="_links">
        <json:object name="read">
          <json:string name="href">http://test.com/items/id3</json:string>
          <json:string name="method">GET</json:string>
        </json:object>
        <json:object name="update">
          <json:string name="href">http://test.com/items/id3</json:string>
          <json:string name="method">PUT</json:string>
        </json:object>
        <json:object name="delete">
          <json:string name="href">http://test.com/items/id3</json:string>
          <json:string name="method">DELETE</json:string>
        </json:object>
      </json:object>
      </json:object>
    </json:array>
  </json:object>
  <json:object name="_links">
    <json:object name="self">
      <json:string name="href">http://test.com/items/?page=1&amp;perPage=10&amp;sort=name&amp;order=asc</json:string>
      <json:string name="method">GET</json:string>
    </json:object>
    <json:object name="create">
      <json:string name="href">http://test.com/items/</json:string>
      <json:string name="method">POST</json:string>
    </json:object>
  </json:object>
  <json:string name="_type">search</json:string>
</json:object>
EOD;

        $decoder = new JsonxTypeDecoder();

        self::assertEquals($expectedData, $decoder->decode($jsonx));
    }

    public function testTypes()
    {
        $jsonx = <<<EOD
<?xml version="1.0" encoding="UTF-8"?>
<json:object xsi:schemaLocation="http://www.datapower.com/schemas/json jsonx.xsd" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:json="http://www.ibm.com/xmlns/prod/2009/jsonx">
  <json:string name="id">id1</json:string>
  <json:string name="name">A fancy Name</json:string>
  <json:object name="treeValues">
    <json:object name="1">
      <json:number name="2">3</json:number>
    </json:object>
  </json:object>
  <json:number name="progress">76.8</json:number>
  <json:boolean name="active">true</json:boolean>
  <json:boolean name="inactive">false</json:boolean>
  <json:string name="phone">0041000000000</json:string>
</json:object>
EOD;

        $decoder = new JsonxTypeDecoder();

        $data = $decoder->decode($jsonx);

        self::assertSame('id1', $data['id']);
        self::assertSame('A fancy Name', $data['name']);
        self::assertSame([1 => [2 => 3]], $data['treeValues']);
        self::assertSame(76.8, $data['progress']);
        self::assertSame(true, $data['active']);
        self::assertSame(false, $data['inactive']);
        self::assertSame('0041000000000', $data['phone']);
    }

    public function testInvalidTag()
    {
        $this->expectException(DeserializerRuntimeException::class);
        $this->expectExceptionMessage('Data is not parsable with content-type: "application/x-jsonx"');
        $decoderType = new JsonxTypeDecoder();
        $decoderType->decode('<?xml version="1.0" encoding="UTF-8"?><unknown></unknown>');
    }

    public function testInvalidType()
    {
        $this->expectException(DeserializerRuntimeException::class);
        $this->expectExceptionMessage('Data is not parsable with content-type: "application/x-jsonx"');
        $decoderType = new JsonxTypeDecoder();
        $decoderType->decode('<?xml version="1.0" encoding="UTF-8"?><json:unknown></json:unknown>');
    }

    public function testInvalidDecode()
    {
        $this->expectException(DeserializerRuntimeException::class);
        $this->expectExceptionMessage('Data is not parsable with content-type: "application/x-jsonx"');
        $decoderType = new JsonxTypeDecoder();
        $decoderType->decode('====');
    }
}
