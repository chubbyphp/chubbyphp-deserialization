<?php

declare(strict_types=1);

namespace Chubbyphp\Deserialization\Mapping;

final class CallableDenormalizationObjectMapping implements DenormalizationObjectMappingInterface
{
    /**
     * @var callable
     */
    private $callable;

    private ?DenormalizationObjectMappingInterface $mapping = null;

    public function __construct(private string $class, callable $callable)
    {
        $this->callable = $callable;
    }

    public function getClass(): string
    {
        return $this->class;
    }

    public function getDenormalizationFactory(string $path, ?string $type = null): callable
    {
        return $this->getMapping()->getDenormalizationFactory($path, $type);
    }

    /**
     * @return array<int, DenormalizationFieldMappingInterface>
     */
    public function getDenormalizationFieldMappings(string $path, ?string $type = null): array
    {
        return $this->getMapping()->getDenormalizationFieldMappings($path, $type);
    }

    private function getMapping(): DenormalizationObjectMappingInterface
    {
        if (!$this->mapping instanceof DenormalizationObjectMappingInterface) {
            $this->mapping = ($this->callable)();
        }

        return $this->mapping;
    }
}
