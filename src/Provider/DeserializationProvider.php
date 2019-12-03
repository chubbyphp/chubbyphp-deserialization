<?php

declare(strict_types=1);

namespace Chubbyphp\Deserialization\Provider;

use Chubbyphp\Deserialization\ServiceProvider\DeserializationServiceProvider;
use Pimple\Container;
use Pimple\ServiceProviderInterface;

final class DeserializationProvider implements ServiceProviderInterface
{
    /**
     * @var DeserializationServiceProvider
     */
    private $serviceProvider;

    public function __construct()
    {
        $this->serviceProvider = new DeserializationServiceProvider();
    }

    public function register(Container $container): void
    {
        $this->serviceProvider->register($container);
    }
}
