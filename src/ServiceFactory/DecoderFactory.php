<?php

declare(strict_types=1);

namespace Chubbyphp\Deserialization\ServiceFactory;

use Chubbyphp\Deserialization\Decoder\Decoder;
use Chubbyphp\Deserialization\Decoder\DecoderInterface;
use Chubbyphp\Deserialization\Decoder\TypeDecoderInterface;
use Chubbyphp\Laminas\Config\Factory\AbstractFactory;
use Psr\Container\ContainerInterface;

/**
 * @deprecated \Chubbyphp\DecodeEncode\Decoder\Decoder
 */
final class DecoderFactory extends AbstractFactory
{
    public function __invoke(ContainerInterface $container): DecoderInterface
    {
        return new Decoder(
            $container->get(TypeDecoderInterface::class.'[]'.$this->name)
        );
    }
}
