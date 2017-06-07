# DeserializationProvider

```php
<?php

use Chubbyphp\Deserialization\Provider\DeserializationProvider;
use MyProject\Deserialization\ModelMapping;
use Pimple\Container;

$container = new Container();
$container->register(new DeserializationProvider());

$container->extend('deserializer.objectmappings', function (array $objectMappings) use ($container) {
    $objectMappings[] = new ModelMapping(...);

    return $objectMappings;
});

$container['deserializer']->validateObject($model);
```
