<?php

declare(strict_types=1);

namespace Chubbyphp\Tests\Deserialize\Resources\Model;

use Chubbyphp\Model\Collection\ModelCollection;
use Chubbyphp\Model\Collection\ModelCollectionInterface;
use Chubbyphp\Model\ModelInterface;

final class One implements ModelInterface
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
     * @var ModelCollectionInterface
     */
    private $manies = [];

    private function __construct()
    {
    }

    /**
     * @param string $id
     * @return One
     */
    public static function create(string $id = null): One
    {
        $one = new self();

        $one->id = $id ?? spl_object_hash($one);
        $one->manies = new ModelCollection(Many::class, 'oneId', $one->id);

        return $one;
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
     * @param Many[] $manies
     * @return $this
     */
    public function setModels(array $manies)
    {
        $this->manies->setModels($manies);

        return $this;
    }

    /**
     * @return Many[]
     */
    public function getManies()
    {
        return $this->manies->getModels();
    }

    /**
     * @param array $data
     *
     * @return One|ModelInterface
     */
    public static function fromPersistence(array $data): ModelInterface
    {
        $one = new self();

        $one->id = $data['id'];
        $one->name = $data['name'];
        $one->manies = $data['manies'];

        return $one;
    }

    /**
     * @return array
     */
    public function toPersistence(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'manies' => $this->manies
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
            'manies' => $this->manies->jsonSerialize()
        ];
    }
}
