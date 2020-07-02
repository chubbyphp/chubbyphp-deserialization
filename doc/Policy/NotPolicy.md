# NotPolicy

```php
<?php

use Chubbyphp\Deserialization\Denormalizer\DenormalizerContextInterface;
use Chubbyphp\Deserialization\Policy\NotPolicy;
use MyProject\Model\Model;
use MyProject\Policy\SomePolicy;

$model = new Model();

/** @var DenormalizerContextInterface $context */
$context = ...;

$policy = new NotPolicy(new SomePolicy());

echo $policy->isCompliantIncludingPath('path', $model, $context);
// 1
```
