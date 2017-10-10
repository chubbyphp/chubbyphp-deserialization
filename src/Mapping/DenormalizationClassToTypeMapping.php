<?php

declare(strict_types=1);

namespace Chubbyphp\Deserialization\Mapping;

final class DenormalizationClassToTypeMapping implements DenormalizationClassToTypeMappingInterface
{
    /**
     * @var string
     */
    private $class;

    /**
     * @var string|null
     */
    private $types;

    /**
     * @param string $class
     * @param string $types
     */
    public function __construct(string $class, array $types)
    {
        $this->class = $class;
        $this->types = $types;
    }

    /**
     * @return string
     */
    public function getClass(): string
    {
        return $this->class;
    }

    /**
     * @return string[]
     */
    public function getTypes(): array
    {
        return $this->types;
    }
}
