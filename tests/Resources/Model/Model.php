<?php

declare(strict_types=1);

namespace Chubbyphp\Tests\Deserialization\Resources\Model;

final class Model
{
    private ?string $name = null;

    private ?OneModel $one = null;

    /**
     * @var AbstractManyModel[]
     */
    private array $manies = [];

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getOne(): ?OneModel
    {
        return $this->one;
    }

    public function setOne(?OneModel $one = null): self
    {
        $this->one = $one;

        return $this;
    }

    /**
     * @return AbstractManyModel[]
     */
    public function getManies(): array
    {
        return $this->manies;
    }

    /**
     * @param AbstractManyModel[] $manies
     */
    public function setManies(array $manies): self
    {
        $this->manies = $manies;

        return $this;
    }
}
