<?php

declare(strict_types=1);

namespace Chubbyphp\Tests\Deserialize\Resources\Model;

final class Many
{
    /**
     * @var string
     */
    private $id;

    /**
     * @var string
     */
    private $name;

    /**
     * @var One
     */
    private $one;

    public function __construct()
    {
        $this->id = spl_object_hash($this);
    }

    /**
     * @return string
     */
    public function getId(): string
    {
        return $this->id;
    }

    /**
     * @param string $name
     * @return self
     */
    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param One  $one
     * @param bool $stopPropagation
     * @return $this
     */
    public function setOne(One $one = null, $stopPropagation = false)
    {
        if(!$stopPropagation) {
            if(!is_null($this->one)) {
                $this->one->removeMany($this, true);
            }
            if(!is_null($one)) {
                $one->addMany($this, true);
            }
        }

        $this->one = $one;

        return $this;
    }

    /**
     * @return One
     */
    public function getOne()
    {
        return $this->one;
    }
}
