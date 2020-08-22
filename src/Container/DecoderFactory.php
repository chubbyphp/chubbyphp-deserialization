<?php

declare(strict_types=1);

namespace Chubbyphp\Deserialization\Container;

use Chubbyphp\Deserialization\Decoder\Decoder;
use Chubbyphp\Deserialization\Decoder\DecoderInterface;
use Chubbyphp\Deserialization\Decoder\TypeDecoderInterface;
use Psr\Container\ContainerInterface;

final class DecoderFactory
{
    public function __invoke(ContainerInterface $container): DecoderInterface
    {
        return new Decoder(
            $container->get(TypeDecoderInterface::class.'[]')
        );
    }
}
