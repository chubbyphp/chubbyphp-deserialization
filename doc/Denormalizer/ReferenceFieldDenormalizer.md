# ReferenceFieldDenormalizer

```php
<?php

use Chubbyphp\Deserialization\Accessor\PropertyAccessor;
use Chubbyphp\Deserialization\Denormalizer\ReferenceFieldDenormalizer;
use MyProject\Model\Model;
use MyProject\Model\ReferenceModel;

$model = new Model;
$context = ...;
$denormalizer = ...;

$fieldDenormalizer = new ReferenceFieldDenormalizer(
    ReferenceModel::class,
    function (string $class, string $id) {},
    new PropertyAccessor('children')
);

$fieldDenormalizer->denormalizeField(
    'reference',
    $model,
    ['name' => 'php'],
    $context,
    $denormalizer
);

echo $model
    ->getReference()
    ->getName();
// 'php'
```
