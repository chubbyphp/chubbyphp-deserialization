<?php

declare(strict_types=1);

namespace Chubbyphp\Tests\Deserialize\Resources\Model\Unidirectional;

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
     * @return $this
     */
    public function addMany(Many $many)
    {
        if (false ===  array_search($many, $this->manies, true)) {
            $this->manies[] = $many;
        }

        return $this;
    }

    /**
     * @param Many $many
     * @return $this
     */
    public function removeMany(Many $many)
    {
        if (false !== $index = array_search($many, $this->manies, true)) {
            unset($this->manies[$index]);
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
