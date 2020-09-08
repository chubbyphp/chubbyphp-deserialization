# DenormalizerFactory

## without name (default)

```php
<?php

use Chubbyphp\Deserialization\ServiceFactory\DenormalizerFactory;
use Psr\Container\ContainerInterface;

/** @var ContainerInterface $container */
$container = ...;

$factory = new DenormalizerFactory();

$denormalizer = $factory($container);
```

## with name `default`

```php
<?php

use Chubbyphp\Deserialization\ServiceFactory\DenormalizerFactory;
use Psr\Container\ContainerInterface;

/** @var ContainerInterface $container */
$container = ...;

$factory = [DenormalizerFactory::class, 'default'];

$denormalizer = $factory($container);
```
