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
     * @var DenormalizationClassToTypeMappingInterface[]
     */
    private $denormalizationClassToTypeMappings;

    /**
     * @param ContainerInterface                           $container
     * @param string                                       $serviceId
     * @param DenormalizationClassToTypeMappingInterface[] $denormalizationClassToTypeMappings
     */
    public function __construct(ContainerInterface $container, $serviceId, array $denormalizationClassToTypeMappings)
    {
        $this->container = $container;
        $this->serviceId = $serviceId;
        $this->denormalizationClassToTypeMappings = $denormalizationClassToTypeMappings;
    }

    /**
     * @return DenormalizationClassToTypeMappingInterface[]
     */
    public function getDenormalizationClassToTypeMappings(): array
    {
        return $this->denormalizationClassToTypeMappings;
    }

    /**
     * @param string $type
     *
     * @return callable
     */
    public function getDenormalizationFactory(string $type): callable
    {
        return $this->container->get($this->serviceId)->getDenormalizationFactory($type);
    }

    /**
     * @param string $type
     *
     * @return DenormalizationFieldMappingInterface[]
     */
    public function getDenormalizationFieldMappings(string $type): array
    {
        return $this->container->get($this->serviceId)->getDenormalizationFieldMappings($type);
    }
}
