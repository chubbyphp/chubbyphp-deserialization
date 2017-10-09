# MethodAccessor

```php
<?php

use Chubbyphp\Deserialization\Accessor\MethodAccessor;

$accessor = new MethodAccessor('key');
$accessor->setValue($object, 'value');
$accessor->getValue($object); // 'value'
```
