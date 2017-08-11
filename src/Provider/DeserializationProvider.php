<?php

declare(strict_types=1);

namespace Chubbyphp\Deserialization\Provider;

use Chubbyphp\Deserialization\Registry\ObjectMappingRegistry;
use Chubbyphp\Deserialization\Deserializer;
use Chubbyphp\Deserialization\Transformer\JsonTransformer;
use Chubbyphp\Deserialization\Transformer\UrlEncodedTransformer;
use Chubbyphp\Deserialization\Transformer\XmlTransformer;
use Chubbyphp\Deserialization\Transformer\YamlTransformer;
use Pimple\Container;
use Pimple\ServiceProviderInterface;

final class DeserializationProvider implements ServiceProviderInterface
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

        $container['deserializer.transformer.json'] = function () {
            return new JsonTransformer();
        };

        $container['deserializer.transformer.urlencoded'] = function () {
            return new UrlEncodedTransformer();
        };

        $container['deserializer.transformer.xml'] = function () {
            return new XmlTransformer();
        };

        $container['deserializer.transformer.yaml'] = function () {
            return new YamlTransformer();
        };
    }
}
