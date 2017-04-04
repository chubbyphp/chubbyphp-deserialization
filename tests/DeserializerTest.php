<?php


namespace Chubbyphp\Tests\Deserialize\Deserializer;

use Chubbyphp\Deserialize\Deserializer;
use Chubbyphp\Deserialize\Registry\ObjectMappingRegistry;
use Chubbyphp\Model\ResolverInterface;
use Chubbyphp\Tests\Deserialize\Resources\Mapping\ManyMapping;
use Chubbyphp\Tests\Deserialize\Resources\Mapping\OneMapping;
use Chubbyphp\Tests\Deserialize\Resources\Mapping\ReferenceMapping;
use Chubbyphp\Tests\Deserialize\Resources\Model\Many;
use Chubbyphp\Tests\Deserialize\Resources\Model\One;
use Chubbyphp\Tests\Deserialize\Resources\Model\Reference;

class DeserializerTest extends \PHPUnit_Framework_TestCase
{
    public function testNew()
    {
        $resolver = $this->getResolver();

        $objectMappingRegistry = new ObjectMappingRegistry([
            new OneMapping(),
            new ManyMapping($resolver),
            new ReferenceMapping()
        ]);

        $deserializer = new Deserializer($objectMappingRegistry);

        /** @var One $one */
        $one = $deserializer->deserializeByClass([
            'name' => 'name1',
            'manies' => [
                0 => [
                    'name' => 'name11',
                    'reference' => [
                        'name' => 'name111'
                    ]
                ],
                1 => [
                    'name' => 'name12',
                    'reference' => [
                        'name' => 'name121'
                    ]
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
        self::assertSame('name11', $many1->getName());
        self::assertSame('name111', $many1->getReference()->getName());

        /** @var Many $many2 */
        $many2 = $manies[1];

        self::assertInstanceOf(Many::class, $many2);
        self::assertNotNull($many2->getId());
        self::assertSame('name12', $many2->getName());
        self::assertSame('name121', $many2->getReference()->getName());
    }

    public function testUpdate()
    {
        $one = One::create();
        $one->setName('name1');

        $many1 = Many::create();
        $many1->setName('name11');

        $one->addMany($many1);

        $many2 = Many::create();
        $many2->setName('name12');

        $one->addMany($many2);

        $reference = Reference::create();
        $reference->setName('name111');

        $resolver = $this->getResolver([
            Reference::class => [
                $reference->getId() => $reference
            ]
        ]);

        $objectMappingRegistry = new ObjectMappingRegistry([
            new OneMapping(),
            new ManyMapping($resolver),
            new ReferenceMapping()
        ]);

        $deserializer = new Deserializer($objectMappingRegistry);

        /** @var One $updatedOne */
        $updatedOne = $deserializer->deserializeByObject([
            'name' => 'name1',
            'manies' => [
                0 => [
                    'name' => 'name11',
                    'reference' => $reference->getId()
                ],
                2 => [
                    'name' => 'name12',
                    'reference' => null
                ]
            ]
        ], $one);

        self::assertSame($one, $updatedOne);

        self::assertNotNull($updatedOne->getId());
        self::assertSame('name1', $updatedOne->getName());

        $manies = $updatedOne->getManies();

        self::assertCount(2, $manies);

        /** @var Many $updatedMany1 */
        $updatedMany1 = $manies[0];

        self::assertSame($many1, $updatedMany1);

        self::assertInstanceOf(Many::class, $updatedMany1);
        self::assertNotNull($updatedMany1->getId());
        self::assertSame('name11', $updatedMany1->getName());
        self::assertSame('name111', $updatedMany1->getReference()->getName());

        /** @var Many $updatedMany2 */
        $updatedMany2 = $manies[1];

        self::assertNotSame($many2, $updatedMany2);

        self::assertInstanceOf(Many::class, $updatedMany2);
        self::assertNotNull($updatedMany2->getId());
        self::assertSame('name12', $updatedMany2->getName());
        self::assertNull($updatedMany2->getReference());
    }

    /**
     * @return ResolverInterface
     */
    private function getResolver(array $models = []): ResolverInterface
    {
        /** @var ResolverInterface|\PHPUnit_Framework_MockObject_MockObject $resolver */
        $resolver = $this->getMockBuilder(ResolverInterface::class)->setMethods(['find'])->getMockForAbstractClass();

        $resolver->expects(self::any())->method('find')->willReturnCallback(
            function (string $modelClass, string $id = null) use ($models) {
                if (null === $id) {
                    return null;
                }

                if (isset($models[$modelClass][$id])) {
                    return $models[$modelClass][$id];
                }
            }
        );

        return $resolver;
    }
}
