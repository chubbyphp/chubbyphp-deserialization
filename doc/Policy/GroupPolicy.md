# GroupPolicy

```php
<?php

use Chubbyphp\Deserialization\Denormalizer\DenormalizerContextInterface;
use Chubbyphp\Deserialization\Policy\GroupPolicy;
use MyProject\Model\Model;

$model = new Model();

/** @var DenormalizerContextInterface $context */
$context = ...;
$context = $context->withAttribute('groups', ['group1']);

$policy = new GroupPolicy(['group1']);

echo $policy->isCompliantIncludingPath('path', $model, $context);
// 1
```
