# CallableDenormalizationObjectMapping

```php
<?php

use Chubbyphp\Deserialization\Mapping\CallableDenormalizationObjectMapping;
use MyProject\Mapping\ModelMapping;
use MyProject\Model\Model;

$objectMapping = new CallableDenormalizationObjectMapping(
    Model::class,
    function () {
        return new ModelMapping();
    }
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
