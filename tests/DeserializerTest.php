<?php


namespace Chubbyphp\Tests\Deserialize;

use Chubbyphp\Deserialize\Deserializer;
use Chubbyphp\Deserialize\Registry\ObjectMappingRegistry;
use Chubbyphp\Tests\Deserialize\Resources\Mapping\ManyMapping;
use Chubbyphp\Tests\Deserialize\Resources\Mapping\OneMapping;
use Chubbyphp\Tests\Deserialize\Resources\Model\One;

class DeserializerTest extends \PHPUnit_Framework_TestCase
{
    public function testNew()
    {
        $objectMappingRegistry = new ObjectMappingRegistry([
            new OneMapping(),
            new ManyMapping()
        ]);

        $deserializer = new Deserializer($objectMappingRegistry);

        $one = $deserializer->deserializeFromArray([
            'id' => 'id1',
            'name' => 'name1',
            'manies' => [
                [
                    'id' => 'id2',
                    'name' => 'name2',
                ],
                [
                    'id' => 'id3',
                    'name' => 'name3',
                ]
            ]
        ], One::class);

        var_dump($one);
    }
}
