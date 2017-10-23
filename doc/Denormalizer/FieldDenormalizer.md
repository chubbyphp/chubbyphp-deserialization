# FieldDenormalizer

```php
<?php

use Chubbyphp\Deserialization\Accessor\PropertyAccessor;
use Chubbyphp\Deserialization\Denormalizer\FieldDenormalizer;
use MyProject\Model\Model;

$model = new Model;
$context = ...;

$fieldDenormalizer = new FieldDenormalizer(
    new PropertyAccessor('name')
);

$fieldDenormalizer->denormalizeField(
    'name',
    $model,
    'php',
    $context
);

echo $model->getName();
// 'php'
```
