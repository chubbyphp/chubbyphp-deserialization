<?php


namespace Chubbyphp\Tests\Deserialize;

use Chubbyphp\Deserialize\Deserializer;
use Chubbyphp\Deserialize\Registry\ObjectMappingRegistry;
use Chubbyphp\Tests\Deserialize\Resources\Mapping\ManyMapping;
use Chubbyphp\Tests\Deserialize\Resources\Mapping\OneMapping;
use Chubbyphp\Tests\Deserialize\Resources\Model\Many;
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

        /** @var One $one */
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

        self::assertInstanceOf(One::class, $one);
        self::assertSame('id1', $one->getId());
        self::assertSame('name1', $one->getName());

        $manies = $one->getManies();

        self::assertCount(2, $manies);

        /** @var Many $many */
        $many = $manies[0];

        self::assertInstanceOf(Many::class, $many);
        self::assertSame('id2', $many->getId());
        self::assertSame('name2', $many->getName());

        /** @var Many $many */
        $many = $manies[1];

        self::assertInstanceOf(Many::class, $many);
        self::assertSame('id3', $many->getId());
        self::assertSame('name3', $many->getName());
    }
}
