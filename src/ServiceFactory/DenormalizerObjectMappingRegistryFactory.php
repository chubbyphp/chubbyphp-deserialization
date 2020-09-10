<?php

declare(strict_types=1);

namespace Chubbyphp\Deserialization\ServiceFactory;

use Chubbyphp\Deserialization\Denormalizer\DenormalizerObjectMappingRegistry;
use Chubbyphp\Deserialization\Denormalizer\DenormalizerObjectMappingRegistryInterface;
use Chubbyphp\Deserialization\Mapping\DenormalizationObjectMappingInterface;
use Chubbyphp\Laminas\Config\Factory\AbstractFactory;
use Psr\Container\ContainerInterface;

final class DenormalizerObjectMappingRegistryFactory extends AbstractFactory
{
    public function __invoke(ContainerInterface $container): DenormalizerObjectMappingRegistryInterface
    {
        return new DenormalizerObjectMappingRegistry(
            $container->get(DenormalizationObjectMappingInterface::class.'[]'.$this->name)
        );
    }
}
