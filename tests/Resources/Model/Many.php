<?php

declare(strict_types=1);

namespace Chubbyphp\Tests\Deserialize\Resources\Model;

use Chubbyphp\Model\ModelInterface;

final class Many implements ModelInterface
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
     * @var string
     */
    private $oneId;

    private function __construct()
    {
    }

    /**
     * @param string $id
     * @return Many
     */
    public static function create(string $id = null): Many
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
     * @return Many|ModelInterface
     */
    public static function fromPersistence(array $data): ModelInterface
    {
        $many = new self();

        $many->id = $data['id'];
        $many->name = $data['name'];
        $many->oneId = $data['oneId'];

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
            'oneId' => $this->oneId,
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
