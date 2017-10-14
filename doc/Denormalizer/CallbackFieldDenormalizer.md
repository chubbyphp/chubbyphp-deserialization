# CallbackFieldDenormalizer

```php
<?php

use Chubbyphp\Deserialization\Denormalizer\CallbackFieldDenormalizer;
use MyProject\Model\Model;

$model = new Model;
$context = ...;

$fieldDenormalizer = new CallbackFieldDenormalizer(
    function (
        string $path,
        $object,
        $value,
        DenormalizerContextInterface $context,
        DenormalizerInterface $denormalizer = null
    ) {
        $object->setName($value);
    }
)

$fieldDenormalizer->denormalize(
    'name',
    $model,
    'php',
    $context
)

echo $model->getName();
// 'php'
```
