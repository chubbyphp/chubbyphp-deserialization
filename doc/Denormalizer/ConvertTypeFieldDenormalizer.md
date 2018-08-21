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

echo $model->getName();
// 5.5
```
