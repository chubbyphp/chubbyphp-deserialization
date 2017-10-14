# MethodAccessor

```php
<?php

use Chubbyphp\Deserialization\Accessor\MethodAccessor;

$accessor = new MethodAccessor('name');
$accessor->setValue($object, 'php');

echo $accessor->getValue($object);
// 'php'
```
