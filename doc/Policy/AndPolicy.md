# AndPolicy

```php
<?php

use Chubbyphp\Deserialization\Denormalizer\DenormalizerContextInterface;
use Chubbyphp\Deserialization\Policy\AndPolicy;
use MyProject\Model\Model;
use MyProject\Policy\AnotherPolicy;
use MyProject\Policy\SomePolicy;

$model = new Model();

/** @var DenormalizerContextInterface $context */
$context = ...;

$policy = new AndPolicy([
    new SomePolicy(),
    new AnotherPolicy(),
]);

echo $policy->isCompliant('path', $model, $context);
// 1
```
