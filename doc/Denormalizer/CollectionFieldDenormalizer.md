# CollectionFieldDenormalizer

```php
<?php

use Chubbyphp\Deserialization\Accessor\PropertyAccessor;
use Chubbyphp\Deserialization\Denormalizer\CollectionFieldDenormalizer;
use MyProject\Model\ParentModel;
use MyProject\Model\ChildModel;

$parentModel = new ParentModel;
$context = ...;
$denormalizer = ...;

$fieldDenormalizer = new CollectionFieldDenormalizer(
    ChildModel::class
    new PropertyAccessor('children')
)

$fieldDenormalizer->denormalize(
    'children',
    $parentModel,
    [['name' => 'php'],
    $context,
    $denormalizer
)

echo $parentModel
    ->getChildren()[0]
    ->getName();
// 'php'
```
