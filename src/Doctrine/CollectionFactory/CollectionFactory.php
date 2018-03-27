<?php

declare(strict_types=1);

namespace Chubbyphp\Deserialization\Doctrine\CollectionFactory;

use Doctrine\Common\Collections\ArrayCollection;

final class CollectionFactory
{
    /**
     * @return ArrayCollection
     */
    public function __invoke(): ArrayCollection
    {
        return new ArrayCollection();
    }
}
