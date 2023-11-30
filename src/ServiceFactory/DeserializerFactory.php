<?php

declare(strict_types=1);

namespace Chubbyphp\Deserialization\ServiceFactory;

use Chubbyphp\DecodeEncode\Decoder\DecoderInterface;
use Chubbyphp\DecodeEncode\ServiceFactory\DecoderFactory;
use Chubbyphp\Deserialization\Denormalizer\DenormalizerInterface;
use Chubbyphp\Deserialization\Deserializer;
use Chubbyphp\Deserialization\DeserializerInterface;
use Chubbyphp\Laminas\Config\Factory\AbstractFactory;
use Psr\Container\ContainerInterface;

final class DeserializerFactory extends AbstractFactory
{
    public function __invoke(ContainerInterface $container): DeserializerInterface
    {
        /** @var DecoderInterface $decoder */
        $decoder = $this->resolveDependency($container, DecoderInterface::class, DecoderFactory::class);

        /** @var DenormalizerInterface $denormalizer */
        $denormalizer = $this->resolveDependency($container, DenormalizerInterface::class, DenormalizerFactory::class);

        return new Deserializer($decoder, $denormalizer);
    }
}
