# DeserializationServiceFactory

```php
<?php

use Chubbyphp\Deserialization\ServiceFactory\DeserializationServiceFactory;
use Chubbyphp\Container\Container;

$container = new Container();
$container->factories((new DeserializationServiceFactory())($container));

$container->get('deserializer')
    ->deserialize(...);

$container->get('deserializer.decoder')
    ->decode(...);

$container->get('deserializer.denormalizer')
    ->denormalize(...);
```
