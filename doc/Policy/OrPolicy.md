# OrPolicy

```php
<?php

use Chubbyphp\Deserialization\Denormalizer\DenormalizerContextInterface;
use Chubbyphp\Deserialization\Policy\OrPolicy;
use MyProject\Model\Model;
use MyProject\Policy\AnotherPolicy;
use MyProject\Policy\SomePolicy;

$model = new Model();

/** @var DenormalizerContextInterface $context */
$context = ...;

$policy = new OrPolicy([
    new SomePolicy(),
    new AnotherPolicy(),
]);

echo $policy->isCompliantIncludingPath('path', $model, $context);
// 1
```
