# ConvertTypeFieldDenormalizer

```php
<?php

use Chubbyphp\Deserialization\Accessor\PropertyAccessor;
use Chubbyphp\Deserialization\Denormalizer\ConvertTypeFieldDenormalizer;
use MyProject\Model\Model;

$model = new Model;
$context = ...;

$fieldDenormalizer = new ConvertTypeFieldDenormalizer(
    new PropertyAccessor('amount'),
    ConvertTypeFieldDenormalizer::TYPE_FLOAT
);

$fieldDenormalizer->denormalizeField(
    'amount',
    $model,
    '5.50',
    $context
);

echo $model->getAmount();
// 5.5

// empty to null
$fieldDenormalizer = new ConvertTypeFieldDenormalizer(
    new PropertyAccessor('amount'),
    ConvertTypeFieldDenormalizer::TYPE_FLOAT,
    true
);

$fieldDenormalizer->denormalizeField(
    'amount',
    $model,
    '',
    $context
);

echo $model->getAmount();
// null
```
