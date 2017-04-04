<?php

declare(strict_types=1);

namespace Chubbyphp\Tests\Deserialize\Resources\Model;

use Chubbyphp\Model\ModelInterface;

final class Reference implements ModelInterface
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
     * @return Reference
     */
    public static function create(string $id = null): Reference
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

    /**
     * @param array $data
     *
     * @return Reference|ModelInterface
     */
    public static function fromPersistence(array $data): ModelInterface
    {
        $reference = new self();

        $reference->id = $data['id'];
        $reference->name = $data['name'];

        return $reference;
    }

    /**
     * @return array
     */
    public function toPersistence(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
        ];
    }

    /**
     * @return array
     */
    public function jsonSerialize(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
        ];
    }
}
