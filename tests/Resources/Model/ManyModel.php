<?php

declare(strict_types=1);

namespace Chubbyphp\Tests\Deserialization\Resources\Model;

final class ManyModel extends AbstractManyModel
{
    /**
     * @var int
     */
    private $value;

    /**
     * @return int
     */
    public function getValue(): int
    {
        return $this->value;
    }

    /**
     * @param int $value
     *
     * @return self
     */
    public function setValue(int $value): self
    {
        $this->value = $value;

        return $this;
    }
}
