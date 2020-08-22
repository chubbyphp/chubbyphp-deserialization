<?php

declare(strict_types=1);

namespace Chubbyphp\Deserialization\Container;

use Chubbyphp\Deserialization\Denormalizer\DenormalizerObjectMappingRegistry;
use Chubbyphp\Deserialization\Denormalizer\DenormalizerObjectMappingRegistryInterface;
use Chubbyphp\Deserialization\Mapping\DenormalizationObjectMappingInterface;
use Psr\Container\ContainerInterface;

final class DenormalizerObjectMappingRegistryFactory
{
    public function __invoke(ContainerInterface $container): DenormalizerObjectMappingRegistryInterface
    {
        return new DenormalizerObjectMappingRegistry($container->get(DenormalizationObjectMappingInterface::class.'[]'));
    }
}
