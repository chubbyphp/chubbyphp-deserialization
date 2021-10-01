# DateTimeImmutableFieldDenormalizer

```php
<?php

use Chubbyphp\Deserialization\Accessor\PropertyAccessor;
use Chubbyphp\Deserialization\Denormalizer\DateTimeImmutableFieldDenormalizer;
use MyProject\Model\Model;

$model = new Model;
$context = ...;

$fieldDenormalizer = new DateTimeImmutableFieldDenormalizer(
    new PropertyAccessor('at')
);

$fieldDenormalizer->denormalizeField(
    'at',
    $model,
    '2017-01-01 22:00:00',
    $context
);

echo $model->getAt()
    ->format('Y-m-d H:i:s');
// '2017-01-01 22:00:00'

// empty to null
$fieldDenormalizer = new DateTimeImmutableFieldDenormalizer(
    new PropertyAccessor('at'),
    null
);

$fieldDenormalizer->denormalizeField(
    'at',
    $model,
    '',
    $context
);

echo $model->getAt();
// null
```
