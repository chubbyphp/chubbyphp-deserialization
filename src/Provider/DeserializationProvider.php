<?php

declare(strict_types=1);

namespace Chubbyphp\Deserialization\Provider;

use Chubbyphp\Deserialization\ServiceProvider\DeserializationServiceProvider;
use Pimple\Container;
use Pimple\ServiceProviderInterface;

/**
 * @deprecated use \Chubbyphp\Deserialization\ServiceProvider\DeserializationServiceProvider
 */
final class DeserializationProvider implements ServiceProviderInterface
{
    /**
     * @var DeserializationServiceProvider
     */
    private $serviceProvider;

    public function __construct()
    {
        @trigger_error(
            sprintf('Use "%s" instead.', DeserializationServiceProvider::class),
            E_USER_DEPRECATED
        );

        $this->serviceProvider = new DeserializationServiceProvider();
    }

    public function register(Container $container): void
    {
        $this->serviceProvider->register($container);
    }
}
