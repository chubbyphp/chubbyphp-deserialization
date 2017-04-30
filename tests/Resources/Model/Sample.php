<?php

declare(strict_types=1);

namespace Chubbyphp\Tests\Deserialize\Resources\Model;

final class Sample
{
    /**
     * @var string
     */
    private $id;

    /**
     * @var string
     */
    private $name;

    private function __construct()
    {
    }

    /**
     * @param string $id
     * @return Sample
     */
    public static function create(string $id = null): Sample
    {
        $reference = new self();

        $reference->id = $id ?? spl_object_hash($reference);

        return $reference;
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
}
