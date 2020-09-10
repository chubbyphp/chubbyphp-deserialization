# DeserializerFactory

## without name (default)

```php
<?php

use Chubbyphp\Deserialization\ServiceFactory\DeserializerFactory;
use Psr\Container\ContainerInterface;

/** @var ContainerInterface $container */
$container = ...;

$factory = new DeserializerFactory();

$deserializer = $factory($container);
```

## with name `default`

```php
<?php

use Chubbyphp\Deserialization\ServiceFactory\DeserializerFactory;
use Psr\Container\ContainerInterface;

/** @var ContainerInterface $container */
$container = ...;

$factory = [DeserializerFactory::class, 'default'];

$deserializer = $factory($container);
```
