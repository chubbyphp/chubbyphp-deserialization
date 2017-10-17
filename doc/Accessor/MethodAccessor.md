# MethodAccessor

```php
<?php

use Chubbyphp\Deserialization\Accessor\MethodAccessor;
use MyProject\Model\Model;

$model = new Model;

$accessor = new MethodAccessor('name');
$accessor->setValue($model, 'php');

echo $accessor->getValue($model);
// 'php'
```
