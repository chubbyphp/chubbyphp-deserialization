<?php

declare(strict_types=1);

namespace Chubbyphp\Deserialize\Provider;

use Chubbyphp\Deserialize\Registry\ObjectMappingRegistry;
use Chubbyphp\Deserialize\Deserializer;
use Pimple\Container;
use Pimple\ServiceProviderInterface;

final class DeserializeProvider implements ServiceProviderInterface
{
    /**
     * @param Container $container
     */
    public function register(Container $container)
    {
        $container['deserializer.emptystringtonull'] = false;

        $container['deserializer.objectmappings'] = function () {
            return [];
        };

        $container['deserializer.objectmappingregistry'] = function () use ($container) {
            return new ObjectMappingRegistry($container['deserializer.objectmappings']);
        };

        $container['deserializer'] = function () use ($container) {
            return new Deserializer(
                $container['deserializer.objectmappingregistry'],
                $container['deserializer.emptystringtonull'],
                $container['logger'] ?? null
            );
        };
    }
}
