<?php

declare(strict_types=1);

namespace Chubbyphp\Deserialize\Mapping;

final class PropertyMapping implements PropertyMappingInterface
{
    /**
     * @var string
     */
    private $name;

    /**
     * @var callable|null
     */
    private $callback;

    /**
     * @param string $name
     * @param callable $callback
     */
    public function __construct(string $name, callable $callback = null)
    {
        $this->name = $name;
        $this->callback = $callback;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return callable|null
     */
    public function getCallback()
    {
        return $this->callback;
    }
}
