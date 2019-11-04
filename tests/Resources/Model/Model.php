<?php

declare(strict_types=1);

namespace Chubbyphp\Tests\Deserialization\Resources\Model;

final class Model
{
    /**
     * @var string
     */
    private $name;

    /**
     * @var OneModel|null
     */
    private $one;

    /**
     * @var AbstractManyModel[]
     */
    private $manies;

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return OneModel|null
     */
    public function getOne()
    {
        return $this->one;
    }

    /**
     * @return self
     */
    public function setOne(OneModel $one = null)
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
