<?php

declare(strict_types=1);

namespace Chubbyphp\Deserialization\ServiceFactory;

use Chubbyphp\Deserialization\Denormalizer\Denormalizer;
use Chubbyphp\Deserialization\Denormalizer\DenormalizerInterface;
use Chubbyphp\Deserialization\Denormalizer\DenormalizerObjectMappingRegistryInterface;
use Chubbyphp\Laminas\Config\Factory\AbstractFactory;
use Psr\Container\ContainerInterface;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;

final class DenormalizerFactory extends AbstractFactory
{
    public function __invoke(ContainerInterface $container): DenormalizerInterface
    {
        /** @var DenormalizerObjectMappingRegistryInterface $denormalizerObjectMappingRegistry */
        $denormalizerObjectMappingRegistry = $this->resolveDependency(
            $container,
            DenormalizerObjectMappingRegistryInterface::class,
            DenormalizerObjectMappingRegistryFactory::class
        );

        /** @var LoggerInterface $logger */
        $logger = $container->has(LoggerInterface::class) ? $container->get(LoggerInterface::class) : new NullLogger();

        return new Denormalizer($denormalizerObjectMappingRegistry, $logger);
    }
}
