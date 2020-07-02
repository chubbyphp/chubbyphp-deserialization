# CallbackPolicyIncludingPath

```php
<?php

use Chubbyphp\Deserialization\Denormalizer\DenormalizerContextInterface;
use Chubbyphp\Deserialization\Policy\CallbackPolicyIncludingPath;
use MyProject\Model\Model;

$model = new Model();

/** @var DenormalizerContextInterface $context */
$context = ...;

$policy = new CallbackPolicyIncludingPath(function (string $path, object $object, DenormalizerContextInterface $context) {
    return true;
});

echo $policy->isCompliantIncludingPath('path', $model, $context);
// 1
```
