<?php

declare(strict_types=1);

namespace Chubbyphp\Tests\DeserializationDoctrine\Resources\Model;

use Doctrine\Common\Persistence\Proxy;

class ProxyModel extends Model implements Proxy
{
    /**
     * @var bool
     */
    private $initialized = false;

    public function __load()
    {
        $this->initialized = true;
    }

    public function __isInitialized()
    {
        return $this->initialized;
    }
}
