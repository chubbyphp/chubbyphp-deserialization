<?php

declare(strict_types=1);

namespace Chubbyphp\Deserialization\Container;

use Chubbyphp\Deserialization\Denormalizer\Denormalizer;
use Chubbyphp\Deserialization\Denormalizer\DenormalizerInterface;
use Chubbyphp\Deserialization\Denormalizer\DenormalizerObjectMappingRegistryInterface;
use Psr\Container\ContainerInterface;
use Psr\Log\LoggerInterface;

/**
 * @deprecated \Chubbyphp\Deserialization\ServiceFactory\DenormalizerFactory
 */
final class DenormalizerFactory
{
    public function __invoke(ContainerInterface $container): DenormalizerInterface
    {
        return new Denormalizer(
            $container->get(DenormalizerObjectMappingRegistryInterface::class),
            $container->get(LoggerInterface::class)
        );
    }
}
