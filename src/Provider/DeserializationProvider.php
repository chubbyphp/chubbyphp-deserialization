<?php

declare(strict_types=1);

namespace Chubbyphp\Deserialization\Provider;

use Chubbyphp\Deserialization\Decoder\Decoder;
use Chubbyphp\Deserialization\Decoder\JsonTypeDecoder;
use Chubbyphp\Deserialization\Decoder\JsonxTypeDecoder;
use Chubbyphp\Deserialization\Decoder\UrlEncodedTypeDecoder;
use Chubbyphp\Deserialization\Decoder\XmlTypeDecoder;
use Chubbyphp\Deserialization\Decoder\YamlTypeDecoder;
use Chubbyphp\Deserialization\Denormalizer\Denormalizer;
use Chubbyphp\Deserialization\Denormalizer\DenormalizerObjectMappingRegistry;
use Chubbyphp\Deserialization\Deserializer;
use Pimple\Container;
use Pimple\ServiceProviderInterface;
use Symfony\Component\Yaml\Yaml;

final class DeserializationProvider implements ServiceProviderInterface
{
    /**
     * @param Container $container
     */
    public function register(Container $container): void
    {
        $container['deserializer'] = function () use ($container) {
            return new Deserializer($container['deserializer.decoder'], $container['deserializer.denormalizer']);
        };

        $container['deserializer.decoder'] = function () use ($container) {
            return new Decoder($container['deserializer.decodertypes']);
        };

        $container['deserializer.decodertypes'] = function () {
            $decoderTypes = [];

            $decoderTypes[] = new JsonTypeDecoder();
            $decoderTypes[] = new JsonxTypeDecoder();
            $decoderTypes[] = new UrlEncodedTypeDecoder();
            $decoderTypes[] = new XmlTypeDecoder();

            if (class_exists(Yaml::class)) {
                $decoderTypes[] = new YamlTypeDecoder();
            }

            @trigger_error(
                'Register the decoder types by yourself:'
                    .' $container[\'deserializer.decodertypes\'] = function () { return [new JsonTypeDecoder()]; };',
                E_USER_DEPRECATED
            );

            return $decoderTypes;
        };

        $container['deserializer.denormalizer'] = function () use ($container) {
            return new Denormalizer(
                $container['deserializer.denormalizer.objectmappingregistry'],
                $container['logger'] ?? null
            );
        };

        $container['deserializer.denormalizer.objectmappingregistry'] = function () use ($container) {
            return new DenormalizerObjectMappingRegistry($container['deserializer.denormalizer.objectmappings']);
        };

        $container['deserializer.denormalizer.objectmappings'] = function () {
            return [];
        };
    }
}
