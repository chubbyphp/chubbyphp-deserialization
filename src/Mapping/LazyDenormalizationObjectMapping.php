<?php

declare(strict_types=1);

namespace Chubbyphp\Deserialization\Mapping;

use Psr\Container\ContainerInterface;

final class LazyDenormalizationObjectMapping implements DenormalizationObjectMappingInterface
{
    private ContainerInterface $container;

    private string $serviceId;

    private string $class;

    public function __construct(ContainerInterface $container, string $serviceId, string $class)
    {
        $this->container = $container;
        $this->serviceId = $serviceId;
        $this->class = $class;
    }

    public function getClass(): string
    {
        return $this->class;
    }

    public function getDenormalizationFactory(string $path, ?string $type = null): callable
    {
        return $this->container->get($this->serviceId)->getDenormalizationFactory($path, $type);
    }

    /**
     * @return array<int, DenormalizationFieldMappingInterface>
     */
    public function getDenormalizationFieldMappings(string $path, ?string $type = null): array
    {
        return $this->container->get($this->serviceId)->getDenormalizationFieldMappings($path, $type);
    }
}
