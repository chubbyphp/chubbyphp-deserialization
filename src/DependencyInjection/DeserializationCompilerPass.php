<?php

declare(strict_types=1);

namespace Chubbyphp\Deserialization\DependencyInjection;

use Chubbyphp\DecodeEncode\Decoder\Decoder;
use Chubbyphp\Deserialization\Denormalizer\Denormalizer;
use Chubbyphp\Deserialization\Denormalizer\DenormalizerObjectMappingRegistry;
use Chubbyphp\Deserialization\Deserializer;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\DependencyInjection\Reference;

final class DeserializationCompilerPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container): void
    {
        $this->registerDecoder($container);
        $this->registerObjectMappingRegistry($container);

        $container
            ->register('chubbyphp.deserializer.denormalizer', Denormalizer::class)
            ->setPublic(true)
            ->setArguments([
                new Reference('chubbyphp.deserializer.denormalizer.objectmappingregistry'),
                new Reference('logger', ContainerInterface::IGNORE_ON_INVALID_REFERENCE),
            ])
        ;

        $container->register('chubbyphp.deserializer', Deserializer::class)->setPublic(true)->setArguments([
            new Reference('chubbyphp.deserializer.decoder'),
            new Reference('chubbyphp.deserializer.denormalizer'),
        ]);
    }

    private function registerDecoder(ContainerBuilder $container): void
    {
        $decoderTypeReferences = [];
        foreach (array_keys($container->findTaggedServiceIds('chubbyphp.deserializer.decoder.type')) as $id) {
            $decoderTypeReferences[] = new Reference($id);
        }

        $container
            ->register('chubbyphp.deserializer.decoder', Decoder::class)
            ->setPublic(true)
            ->setArguments([$decoderTypeReferences])
        ;
    }

    private function registerObjectMappingRegistry(ContainerBuilder $container): void
    {
        $denormalizerObjectMappingReferences = [];

        $taggedServiceIds = $container->findTaggedServiceIds('chubbyphp.deserializer.denormalizer.objectmapping');
        foreach (array_keys($taggedServiceIds) as $id) {
            $denormalizerObjectMappingReferences[] = new Reference($id);
        }

        $container
            ->register(
                'chubbyphp.deserializer.denormalizer.objectmappingregistry',
                DenormalizerObjectMappingRegistry::class
            )
            ->setPublic(true)
            ->setArguments([$denormalizerObjectMappingReferences])
        ;
    }
}
