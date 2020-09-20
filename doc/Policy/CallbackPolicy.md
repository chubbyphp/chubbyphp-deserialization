# CallbackPolicy

```php
<?php

use Chubbyphp\Deserialization\Denormalizer\DenormalizerContextInterface;
use Chubbyphp\Deserialization\Policy\CallbackPolicy;
use MyProject\Model\Model;

$model = new Model();

/** @var DenormalizerContextInterface $context */
$context = ...;

$policy = new CallbackPolicy(function (string $path, object $object, DenormalizerContextInterface $context) {
    return true;
});

echo $policy->isCompliant('path', $model, $context);
// 1
```
