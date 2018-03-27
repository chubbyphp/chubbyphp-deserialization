# EmbedManyFieldDenormalizer

```php
<?php

use Chubbyphp\Deserialization\Accessor\PropertyAccessor;
use Chubbyphp\Deserialization\Doctrine\CollectionFactory\CollectionFactory as DoctrineCollectionFactory;
use Chubbyphp\Deserialization\Denormalizer\Relation\EmbedManyFieldDenormalizer;
use MyProject\Model\ParentModel;
use MyProject\Model\ChildModel;

$parentModel = new ParentModel;
$context = ...;
$denormalizer = ...;

$fieldDenormalizer = new EmbedManyFieldDenormalizer(
    ChildModel::class,
    new PropertyAccessor('children'),
    new DoctrineCollectionFactory() // if you work with doctrine collections
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
