<?php

declare(strict_types=1);

namespace Chubbyphp\Deserialization\Mapping;

use Psr\Container\ContainerInterface;

final class LazyDenormalizationObjectMapping implements DenormalizationObjectMappingInterface
{
    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * @var string
     */
    private $serviceId;

    /**
     * @var string
     */
    private $class;

    /**
     * @param string $serviceId
     */
    public function __construct(ContainerInterface $container, $serviceId, string $class)
    {
        $this->container = $container;
        $this->serviceId = $serviceId;
        $this->class = $class;
    }

    public function getClass(): string
    {
        return $this->class;
    }

    public function getDenormalizationFactory(string $path, string $type = null): callable
    {
        return $this->container->get($this->serviceId)->getDenormalizationFactory($path, $type);
    }

    /**
     * @return DenormalizationFieldMappingInterface[]
     */
    public function getDenormalizationFieldMappings(string $path, string $type = null): array
    {
        return $this->container->get($this->serviceId)->getDenormalizationFieldMappings($path, $type);
    }
}
