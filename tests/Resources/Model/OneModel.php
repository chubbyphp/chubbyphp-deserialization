<?php

declare(strict_types=1);

namespace Chubbyphp\Tests\Deserialization\Resources\Model;

final class OneModel
{
    /**
     * @var string
     */
    private $name;

    /**
     * @var string|null
     */
    private $value;

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
     * @return string|null
     */
    public function getValue()
    {
        return $this->value;
    }

    public function setValue(string $value = null): self
    {
        $this->value = $value;

        return $this;
    }
}
