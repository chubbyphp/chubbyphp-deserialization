<?php

declare(strict_types=1);

namespace Chubbyphp\Deserialization\ServiceFactory;

use Chubbyphp\Deserialization\Mapping\DenormalizationFieldMappingFactory;
use Chubbyphp\Deserialization\Mapping\DenormalizationFieldMappingFactoryInterface;
use Chubbyphp\Laminas\Config\Factory\AbstractFactory;
use Psr\Container\ContainerInterface;

final class DenormalizationFieldMappingFactoryFactory extends AbstractFactory
{
    public function __invoke(ContainerInterface $container): DenormalizationFieldMappingFactoryInterface
    {
        return new DenormalizationFieldMappingFactory();
    }
}
