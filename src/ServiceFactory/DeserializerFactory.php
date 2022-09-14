<?php

declare(strict_types=1);

namespace Chubbyphp\Deserialization\ServiceFactory;

use Chubbyphp\DecodeEncode\Decoder\DecoderInterface;
use Chubbyphp\DecodeEncode\ServiceFactory\DecoderFactory;
use Chubbyphp\Deserialization\Decoder\DecoderInterface as OldDecoderInterface;
use Chubbyphp\Deserialization\Denormalizer\DenormalizerInterface;
use Chubbyphp\Deserialization\Deserializer;
use Chubbyphp\Deserialization\DeserializerInterface;
use Chubbyphp\Laminas\Config\Factory\AbstractFactory;
use Psr\Container\ContainerInterface;

final class DeserializerFactory extends AbstractFactory
{
    public function __invoke(ContainerInterface $container): DeserializerInterface
    {
        if ($container->has(OldDecoderInterface::class.$this->name)) {
            @trigger_error(
                sprintf(
                    '%s use %s',
                    OldDecoderInterface::class,
                    DecoderInterface::class
                ),
                E_USER_DEPRECATED
            );

            /** @var OldDecoderInterface $decoder */
            $decoder = $container->get(OldDecoderInterface::class.$this->name);
        } else {
            /** @var DecoderInterface $decoder */
            $decoder = $this->resolveDependency($container, DecoderInterface::class, DecoderFactory::class);
        }

        /** @var DenormalizerInterface $denormalizer */
        $denormalizer = $this->resolveDependency($container, DenormalizerInterface::class, DenormalizerFactory::class);

        return new Deserializer($decoder, $denormalizer);
    }
}
