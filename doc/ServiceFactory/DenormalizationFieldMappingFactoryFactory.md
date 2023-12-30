# DenormalizationFieldMappingFactoryFactory

## without name (default)

```php
<?php

use Chubbyphp\Deserialization\Decoder\TypeDecoderInterface;
use Chubbyphp\Deserialization\ServiceFactory\DenormalizationFieldMappingFactoryFactory;
use Psr\Container\ContainerInterface;

/** @var ContainerInterface $container */
$container = ...;

// $container->get(TypeDecoderInterface::class.'[]')

$factory = new DenormalizationFieldMappingFactoryFactory();

$decoder = $factory($container);
```

## with name `default`

```php
<?php

use Chubbyphp\Deserialization\Decoder\TypeDecoderInterface;
use Chubbyphp\Deserialization\ServiceFactory\DenormalizationFieldMappingFactoryFactory;
use Psr\Container\ContainerInterface;

/** @var ContainerInterface $container */
$container = ...;

// $container->get(TypeDecoderInterface::class.'[]default')

$factory = [DenormalizationFieldMappingFactoryFactory::class, 'default'];

$decoder = $factory($container);
```
