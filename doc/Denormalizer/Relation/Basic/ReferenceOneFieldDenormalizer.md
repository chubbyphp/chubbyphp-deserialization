# ReferenceOneFieldDenormalizer

```php
<?php

use Chubbyphp\Deserialization\Accessor\PropertyAccessor;
use Chubbyphp\Deserialization\Denormalizer\Relation\Basic\ReferenceOneFieldDenormalizer;
use MyProject\Model\Model;
use MyProject\Model\ReferenceModel;

$model = new Model;
$context = ...;
$denormalizer = ...;

$fieldDenormalizer = new ReferenceOneFieldDenormalizer(
    function (string $id) {
        return;
    },
    new PropertyAccessor('children')
);

$fieldDenormalizer->denormalizeField(
    'reference',
    $model,
    '60a9ee14-64d6-4992-8042-8d1528ac02d6',
    $context,
    $denormalizer
);

echo $model
    ->getReference()
    ->getName();
// 'php'
```
