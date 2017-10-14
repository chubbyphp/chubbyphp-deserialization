# MethodAccessor

```php
<?php

use Chubbyphp\Deserialization\Accessor\MethodAccessor;

$accessor = new MethodAccessor('key');
$accessor->setValue($object, 'value');

echo $accessor->getValue($object);
// 'value'
```
