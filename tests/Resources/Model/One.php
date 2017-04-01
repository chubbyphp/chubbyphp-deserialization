<?php

declare(strict_types=1);

namespace Chubbyphp\Tests\Deserialize\Resources\Model;

final class One
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
     * @var Many[]
     */
    private $manies = [];

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
     * @param Many $many
     * @param bool $stopPropagation
     * @return $this
     */
    public function addMany(Many $many, $stopPropagation = false)
    {
        if (false ===  array_search($many, $this->manies, true)) {
            $this->manies[] = $many;
        }

        if(!$stopPropagation) {
            $many->setOne($this, true);
        }

        return $this;
    }

    /**
     * @param Many $many
     * @param bool $stopPropagation
     * @return $this
     */
    public function removeMany(Many $many, $stopPropagation = false)
    {
        if (false !== $index = array_search($many, $this->manies, true)) {
            unset($this->manies[$index]);
        }

        if(!$stopPropagation) {
            $many->setOne(null, true);
        }

        return $this;
    }

    /**
     * @return Many[]
     */
    public function getManies()
    {
        return $this->manies;
    }
}
