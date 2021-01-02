<?php

declare(strict_types=1);

namespace Chubbyphp\Deserialization\ServiceProvider;

use Chubbyphp\Deserialization\Decoder\Decoder;
use Chubbyphp\Deserialization\Denormalizer\Denormalizer;
use Chubbyphp\Deserialization\Denormalizer\DenormalizerObjectMappingRegistry;
use Chubbyphp\Deserialization\Deserializer;
use Pimple\Container;
use Pimple\ServiceProviderInterface;

final class DeserializationServiceProvider implements ServiceProviderInterface
{
    public function register(Container $container): void
    {
        $container['deserializer'] = static fn () => new Deserializer($container['deserializer.decoder'], $container['deserializer.denormalizer']);

        $container['deserializer.decoder'] = static fn () => new Decoder($container['deserializer.decodertypes']);

        $container['deserializer.decodertypes'] = static fn () => [];

        $container['deserializer.denormalizer'] = static fn () => new Denormalizer(
            $container['deserializer.denormalizer.objectmappingregistry'],
            $container['logger'] ?? null
        );

        $container['deserializer.denormalizer.objectmappingregistry'] = static fn () => new DenormalizerObjectMappingRegistry($container['deserializer.denormalizer.objectmappings']);

        $container['deserializer.denormalizer.objectmappings'] = static fn () => [];
    }
}
