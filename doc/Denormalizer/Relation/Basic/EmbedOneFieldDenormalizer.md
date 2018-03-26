# EmbedOneFieldDenormalizer

```php
<?php

use Chubbyphp\Deserialization\Accessor\PropertyAccessor;
use Chubbyphp\Deserialization\Denormalizer\Relation\Basic\EmbedOneFieldDenormalizer;
use MyProject\Model\Model;
use MyProject\Model\ReferenceModel;

$model = new Model;
$context = ...;
$denormalizer = ...;

$fieldDenormalizer = new EmbedOneFieldDenormalizer(
    ReferenceModel::class,
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
