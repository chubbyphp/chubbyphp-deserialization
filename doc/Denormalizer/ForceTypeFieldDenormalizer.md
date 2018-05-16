# ForceTypeFieldDenormalizer

```php
<?php

use Chubbyphp\Deserialization\Accessor\PropertyAccessor;
use Chubbyphp\Deserialization\Denormalizer\ForceTypeFieldDenormalizer;
use MyProject\Model\Model;

$model = new Model;
$context = ...;

$fieldDenormalizer = new ForceTypeFieldDenormalizer(
    new PropertyAccessor('value'),
    ForceTypeFieldDenormalizer::TYPE_FLOAT
);

$fieldDenormalizer->denormalizeField(
    'value',
    $model,
    '5.5',
    $context
);

echo $model->getValue();
// 5.5
```
