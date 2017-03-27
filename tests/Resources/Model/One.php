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
     * @return self
     */
    public function addMany(Many $many): self
    {
        $this->manies[spl_object_hash($many)] = $many;

        return $this;
    }

    /**
     * @param Many $many
     * @return self
     */
    public function removeMany(Many $many): self
    {
        $hash = spl_object_hash($many);
        if (isset($this->manies[$hash])) {
            unset($this->manies[$hash]);
        }

        return $this;
    }

    /**
     * @return array
     */
    public function getManies(): array
    {
        return $this->manies;
    }
}
