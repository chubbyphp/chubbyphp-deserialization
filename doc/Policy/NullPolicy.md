# NullPolicy

```php
<?php

use Chubbyphp\Deserialization\Denormalizer\DenormalizerContextInterface;
use Chubbyphp\Deserialization\Policy\NullPolicy;
use MyProject\Model\Model;

$model = new Model();

/** @var DenormalizerContextInterface $context */
$context = ...;

$policy = new NullPolicy();

echo $policy->isCompliant('path', $model, $context);
// 1
```
