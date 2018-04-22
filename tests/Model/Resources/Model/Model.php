<?php

declare(strict_types=1);

namespace Chubbyphp\Tests\DeserializationModel\Resources\Model;

use Chubbyphp\Model\ModelInterface;

final class Model implements ModelInterface
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
     *
     * @return self
     */
    public static function create(string $id = null): self
    {
        $many = new self();

        $many->id = $id ?? spl_object_hash($many);

        return $many;
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
     *
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
     * @return Model|ModelInterface
     */
    public static function fromPersistence(array $data): ModelInterface
    {
        $many = new self();

        $many->id = $data['id'];
        $many->name = $data['name'];

        return $many;
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
}
