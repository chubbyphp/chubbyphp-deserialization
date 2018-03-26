# EmbedManyFieldDenormalizer

```php
<?php

use Chubbyphp\Deserialization\Accessor\PropertyAccessor;
use Chubbyphp\Deserialization\Denormalizer\Relation\Doctrine\EmbedManyFieldDenormalizer;
use MyProject\Model\ParentModel;
use MyProject\Model\ChildModel;

$parentModel = new ParentModel;
$context = ...;
$denormalizer = ...;

$fieldDenormalizer = new EmbedManyFieldDenormalizer(
    ChildModel::class,
    new PropertyAccessor('children')
);

$fieldDenormalizer->denormalizeField(
    'children',
    $parentModel,
    [['name' => 'php']],
    $context,
    $denormalizer
);

echo $parentModel
    ->getChildren()[0]
    ->getName();
// 'php'
```
