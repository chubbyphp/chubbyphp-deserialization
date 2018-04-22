<?php

declare(strict_types=1);

namespace Chubbyphp\Tests\DeserializationDoctrine\Resources\Model;

class Model
{
    /**
     * @var string
     */
    protected $id;

    /**
     * @var string
     */
    protected $name;

    private function __construct()
    {
    }

    /**
     * @param string $id
     *
     * @return static
     */
    public static function create(string $id = null)
    {
        $model = new static();

        $model->id = $id ?? spl_object_hash($model);

        return $model;
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
}
