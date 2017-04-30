<?php


namespace Chubbyphp\Tests\Deserialize\Deserializer;

use Chubbyphp\Deserialize\Deserializer;
use Chubbyphp\Deserialize\Registry\ObjectMappingRegistry;
use Chubbyphp\Tests\Deserialize\Resources\Mapping\SampleMapping;
use Chubbyphp\Tests\Deserialize\Resources\Model\Sample;

class DeserializerTest extends \PHPUnit_Framework_TestCase
{
    public function testNew()
    {
        $objectMappingRegistry = new ObjectMappingRegistry([
            new SampleMapping()
        ]);

        $deserializer = new Deserializer($objectMappingRegistry);

        /** @var Sample $sample */
        $sample = $deserializer->deserializeByClass([
            'name' => 'name1',
            'unknownField' => 'dummy'
        ], Sample::class);

        self::assertInstanceOf(Sample::class, $sample);
        self::assertNotNull($sample->getId());
        self::assertSame('name1', $sample->getName());
    }

    public function testUpdate()
    {
        $sample = Sample::create();
        $sample->setName('name1');

        $objectMappingRegistry = new ObjectMappingRegistry([
            new SampleMapping()
        ]);

        $deserializer = new Deserializer($objectMappingRegistry);

        /** @var Sample $updatedSample */
        $updatedSample = $deserializer->deserializeByObject([
            'name' => 'name2'
        ], $sample);

        self::assertSame($sample, $updatedSample);

        self::assertNotNull($updatedSample->getId());
        self::assertSame('name2', $updatedSample->getName());
    }
}
