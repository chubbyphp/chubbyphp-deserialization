<?php

declare(strict_types=1);

namespace Chubbyphp\Tests\Deserialization\Doctrine\CollectionFactory;

use Chubbyphp\Deserialization\Doctrine\CollectionFactory\CollectionFactory;
use Doctrine\Common\Collections\ArrayCollection;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Chubbyphp\Deserialization\Doctrine\CollectionFactory\CollectionFactory
 */
class CollectionFactoryTest extends TestCase
{
    public function testInvoke()
    {
        $collectionFactory = new CollectionFactory();

        $collection1 = $collectionFactory();
        $collection2 = $collectionFactory();

        self::assertInstanceOf(ArrayCollection::class, $collection1);
        self::assertInstanceOf(ArrayCollection::class, $collection2);

        self::assertNotSame($collection1, $collection2);
    }
}
