# DateTimeFieldDenormalizer

```php
<?php

use Chubbyphp\Deserialization\Accessor\PropertyAccessor;
use Chubbyphp\Deserialization\Denormalizer\DateTimeFieldDenormalizer;
use Chubbyphp\Deserialization\Denormalizer\FieldDenormalizer;
use MyProject\Model\Model;

$model = new Model;
$context = ...;

$fieldDenormalizer = new DateTimeFieldDenormalizer(
    new FieldDenormalizer(
        new PropertyAccessor('at')
    )
);

$fieldDenormalizer->denormalizeField(
    'at',
    $model,
    new \DateTime('2017-01-01 22:00:00'),
    $context
);

echo $model->getAt()
    ->format('Y-m-d H:i:s');
// '2017-01-01 22:00:00'
```
