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
     * @var callable
     */
    private $isDenormalizationResponsible;

    /**
     * @param ContainerInterface $container
     * @param string             $serviceId
     * @param callable           $isDenormalizationResponsible
     */
    public function __construct(ContainerInterface $container, $serviceId, callable $isDenormalizationResponsible)
    {
        $this->container = $container;
        $this->serviceId = $serviceId;
        $this->isDenormalizationResponsible = $isDenormalizationResponsible;
    }

    /**
     * @param string $class
     *
     * @return bool
     */
    public function isDenormalizationResponsible(string $class): bool
    {
        $isDenormalizationResponsible = $this->isDenormalizationResponsible;

        return $isDenormalizationResponsible($class);
    }

    /**
     * @param string|null $type
     *
     * @return callable
     */
    public function getDenormalizationFactory(string $type = null): callable
    {
        return $this->container->get($this->serviceId)->getDenormalizationFactory($type);
    }

    /**
     * @param string|null $type
     *
     * @return DenormalizationFieldMappingInterface[]
     */
    public function getDenormalizationFieldMappings(string $type = null): array
    {
        return $this->container->get($this->serviceId)->getDenormalizationFieldMappings($type);
    }
}
