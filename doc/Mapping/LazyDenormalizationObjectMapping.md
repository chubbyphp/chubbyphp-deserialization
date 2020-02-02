# LazyDenormalizationObjectMapping

```php
<?php

use Chubbyphp\Deserialization\Mapping\LazyDenormalizationObjectMapping;
use MyProject\Model\Model;

$container = ...;

$objectMapping = new LazyDenormalizationObjectMapping(
    $container,
    'myproject.denormalizer.mapping.model',
    Model::class
);

echo $objectMapping->getClass();
// 'MyProject\Model\Model'

$callable = $objectMapping->getFactory('');
$model = $callable();

echo get_class($model);
// 'MyProject\Model\Model'

$objectMapping->getDenormalizationFieldMappings('');
// array<int, DenormalizationFieldMappingInterface>
```
