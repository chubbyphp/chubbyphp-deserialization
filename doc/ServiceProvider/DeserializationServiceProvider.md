# DeserializationServiceProvider

```php
<?php

use Chubbyphp\Deserialization\ServiceProvider\DeserializationServiceProvider;
use Pimple\Container;

$container = new Container();
$container->register(new DeserializationServiceProvider);

$container['deserializer']
    ->deserialize(...);

$container['deserializer.decoder']
    ->decode(...);

$container['deserializer.denormalizer']
    ->denormalize(...);
```
