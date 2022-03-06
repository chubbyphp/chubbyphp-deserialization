<?php

declare(strict_types=1);

namespace Chubbyphp\Tests\Deserialization\Resources\Model;

abstract class AbstractManyModel
{
    protected ?string $name = null;

    private ?string $address = null;

    final public function getName(): string
    {
        return $this->name;
    }

    final public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    final public function getAddress(): string
    {
        return $this->address;
    }

    final public function setAddress(string $address): void
    {
        $this->address = $address;
    }
}
