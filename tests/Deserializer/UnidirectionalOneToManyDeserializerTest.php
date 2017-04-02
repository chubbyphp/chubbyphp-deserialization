<?php


namespace Chubbyphp\Tests\Deserialize\Deserializer;

use Chubbyphp\Deserialize\Deserializer;
use Chubbyphp\Deserialize\Registry\ObjectMappingRegistry;
use Chubbyphp\Tests\Deserialize\Resources\Mapping\Unidirectional\ManyMapping;
use Chubbyphp\Tests\Deserialize\Resources\Mapping\Unidirectional\OneMapping;
use Chubbyphp\Tests\Deserialize\Resources\Model\Unidirectional\Many;
use Chubbyphp\Tests\Deserialize\Resources\Model\Unidirectional\One;

class UnidirectionalOneToManyDeserializerTest extends \PHPUnit_Framework_TestCase
{
    public function testNew()
    {
        $objectMappingRegistry = new ObjectMappingRegistry([
            new OneMapping(),
            new ManyMapping()
        ]);

        $deserializer = new Deserializer($objectMappingRegistry);

        /** @var One $one */
        $one = $deserializer->deserializeByClass([
            'name' => 'name1',
            'manies' => [
                0 => [
                    'name' => 'name2',
                ],
                1 => [
                    'name' => 'name3',
                ]
            ]
        ], One::class);

        self::assertInstanceOf(One::class, $one);
        self::assertNotNull($one->getId());
        self::assertSame('name1', $one->getName());

        $manies = $one->getManies();

        self::assertCount(2, $manies);

        /** @var Many $many1 */
        $many1 = $manies[0];

        self::assertInstanceOf(Many::class, $many1);
        self::assertNotNull($many1->getId());
        self::assertSame('name2', $many1->getName());

        /** @var Many $many2 */
        $many2 = $manies[1];

        self::assertInstanceOf(Many::class, $many2);
        self::assertNotNull($many2->getId());
        self::assertSame('name3', $many2->getName());
    }

    public function testUpdate()
    {
        $one = new One();
        $one->setName('name1');

        $many1 = new Many();
        $many1->setName('name2');

        $one->addMany($many1);

        $many2 = new Many();
        $many2->setName('name3');

        $one->addMany($many2);

        $objectMappingRegistry = new ObjectMappingRegistry([
            new OneMapping(),
            new ManyMapping()
        ]);

        $deserializer = new Deserializer($objectMappingRegistry);

        /** @var One $updatedOne */
        $updatedOne = $deserializer->deserializeByObject([
            'name' => 'name11',
            'manies' => [
                0 => [
                    'name' => 'name22',
                ],
                2 => [
                    'name' => 'name33',
                ]
            ]
        ], $one);

        self::assertSame($one, $updatedOne);

        self::assertNotNull($updatedOne->getId());
        self::assertSame('name11', $updatedOne->getName());

        $manies = $updatedOne->getManies();

        self::assertCount(2, $manies);

        /** @var Many $updatedMany1 */
        $updatedMany1 = $manies[0];

        self::assertSame($many1, $updatedMany1);

        self::assertNotNull($updatedMany1->getId());
        self::assertSame('name22', $updatedMany1->getName());

        /** @var Many $updatedMany2 */
        $updatedMany2 = $manies[2];

        self::assertNotSame($many2, $updatedMany2);

        self::assertInstanceOf(Many::class, $updatedMany2);
        self::assertNotNull($updatedMany2->getId());
        self::assertSame('name33', $updatedMany2->getName());
    }
}
