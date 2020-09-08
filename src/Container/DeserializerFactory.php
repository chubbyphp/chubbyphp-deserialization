<?php

declare(strict_types=1);

namespace Chubbyphp\Deserialization\Container;

use Chubbyphp\Deserialization\Decoder\DecoderInterface;
use Chubbyphp\Deserialization\Denormalizer\DenormalizerInterface;
use Chubbyphp\Deserialization\Deserializer;
use Chubbyphp\Deserialization\DeserializerInterface;
use Psr\Container\ContainerInterface;

/**
 * @deprecated \Chubbyphp\Deserialization\ServiceFactory\DeserializerFactory
 */
final class DeserializerFactory
{
    public function __invoke(ContainerInterface $container): DeserializerInterface
    {
        return new Deserializer(
            $container->get(DecoderInterface::class),
            $container->get(DenormalizerInterface::class)
        );
    }
}
