<?php

declare(strict_types=1);

namespace Chubbyphp\Deserialization\Provider;

use Chubbyphp\Deserialization\Decoder\Decoder;
use Chubbyphp\Deserialization\Decoder\JsonDecoderType;
use Chubbyphp\Deserialization\Decoder\UrlEncodedDecoderType;
use Chubbyphp\Deserialization\Decoder\XmlDecoderType;
use Chubbyphp\Deserialization\Decoder\YamlDecoderType;
use Chubbyphp\Deserialization\Denormalizer\Denormalizer;
use Chubbyphp\Deserialization\Deserializer;
use Pimple\Container;
use Pimple\ServiceProviderInterface;
use Symfony\Component\Yaml\Yaml;

final class DeserializationProvider implements ServiceProviderInterface
{
    /**
     * @param Container $container
     */
    public function register(Container $container)
    {
        $container['deserializer'] = function () use ($container) {
            return new Deserializer($container['deserializer.decoder'], $container['deserializer.denormalizer']);
        };

        $container['deserializer.decoder'] = function () use ($container) {
            return new Decoder($container['deserializer.decodertypes']);
        };

        $container['deserializer.decodertypes'] = function () {
            $decoderTypes = [];

            $decoderTypes[] = new JsonDecoderType();
            $decoderTypes[] = new UrlEncodedDecoderType();
            $decoderTypes[] = new XmlDecoderType();

            if (class_exists(Yaml::class)) {
                $decoderTypes[] = new YamlDecoderType();
            }

            return $decoderTypes;
        };

        $container['deserializer.denormalizer'] = function () use ($container) {
            return new Denormalizer(
                $container['deserializer.denormalizer.objectmappings'],
                $container['logger'] ?? null
            );
        };

        $container['deserializer.denormalizer.objectmappings'] = function () {
            return [];
        };
    }
}
