<?php

declare(strict_types=1);

namespace Chubbyphp\Deserialization\DependencyInjection;

use Chubbyphp\Deserialization\Decoder\Decoder;
use Chubbyphp\Deserialization\Decoder\JsonTypeDecoder;
use Chubbyphp\Deserialization\Decoder\UrlEncodedTypeDecoder;
use Chubbyphp\Deserialization\Decoder\XmlTypeDecoder;
use Chubbyphp\Deserialization\Decoder\YamlTypeDecoder;
use Chubbyphp\Deserialization\Denormalizer\Denormalizer;
use Chubbyphp\Deserialization\Denormalizer\DenormalizerObjectMappingRegistry;
use Chubbyphp\Deserialization\Deserializer;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\Yaml\Yaml;

final class DeserializationCompilerPass implements CompilerPassInterface
{
    /**
     * @param ContainerBuilder $container
     */
    public function process(ContainerBuilder $container)
    {
        $container->register('chubbyphp.deserializer', Deserializer::class)->setPublic(true)->setArguments([
            new Reference('chubbyphp.deserializer.decoder'),
            new Reference('chubbyphp.deserializer.denormalizer'),
        ]);

        $container
            ->register('chubbyphp.deserializer.decoder.type.json', JsonTypeDecoder::class)
            ->addTag('chubbyphp.deserializer.decoder.type');

        $container
            ->register('chubbyphp.deserializer.decoder.type.urlencoded', UrlEncodedTypeDecoder::class)
            ->addTag('chubbyphp.deserializer.decoder.type');

        $container
            ->register('chubbyphp.deserializer.decoder.type.xml', XmlTypeDecoder::class)
            ->addTag('chubbyphp.deserializer.decoder.type');

        if (class_exists(Yaml::class)) {
            $container
                ->register('chubbyphp.deserializer.decoder.type.yml', YamlTypeDecoder::class)
                ->addTag('chubbyphp.deserializer.decoder.type');
        }

        $decoderTypeReferences = [];
        foreach ($container->findTaggedServiceIds('chubbyphp.deserializer.decoder.type') as $id => $tags) {
            $decoderTypeReferences[] = new Reference($id);
        }

        $container
            ->register('chubbyphp.deserializer.decoder', Decoder::class)
            ->setPublic(true)
            ->setArguments([$decoderTypeReferences]);

        $container
            ->register('chubbyphp.deserializer.denormalizer', Denormalizer::class)
            ->setPublic(true)
            ->setArguments([
                new Reference('chubbyphp.deserializer.denormalizer.objectmappingregistry'),
                new Reference('logger', ContainerInterface::IGNORE_ON_INVALID_REFERENCE),
            ]);

        $denormalizerObjectMappingReferences = [];
        foreach ($container->findTaggedServiceIds('deserializer.denormalizer.objectmapping') as $id => $tags) {
            $denormalizerObjectMappingReferences[] = new Reference($id);
        }

        $container
            ->register(
                'chubbyphp.deserializer.denormalizer.objectmappingregistry',
                DenormalizerObjectMappingRegistry::class
            )
            ->setPublic(true)
            ->setArguments([$denormalizerObjectMappingReferences]);
    }
}
