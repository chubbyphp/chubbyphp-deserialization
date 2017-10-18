# Denormalizer

```php
<?php

use Chubbyphp\Deserialization\Denormalizer\Denormalizer;
use Chubbyphp\Deserialization\Denormalizer\DenormalizerObjectMappingRegistry;
use MyProject\Deserialization\ModelMapping;
use MyProject\Model\Model;

$logger = ...;

$denormalizer = new Denormalizer(
    new DenormalizerObjectMappingRegistry([
        new ModelMapping()
    ]),
    $logger
);

$model = $denormalizer->denormalize(
    Model::class,
    ['name' => 'php']
);

echo $model->getName();
// 'php'
```