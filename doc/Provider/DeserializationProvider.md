# DeserializationProvider

```php
<?php

use Chubbyphp\Deserialization\Provider\DeserializationProvider;
use Pimple\Container;

$container = new Container();
$container->register(new DeserializationProvider);

$container['deserializer']
    ->deserialize(...);

$container['deserializer.decoder']
    ->decode(...);

$container['deserializer.denormalizer']
    ->denormalize(...);
```
