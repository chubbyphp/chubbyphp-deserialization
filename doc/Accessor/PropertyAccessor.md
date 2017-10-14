# PropertyAccessor

```php
<?php

use Chubbyphp\Deserialization\Accessor\PropertyAccessor;

$accessor = new PropertyAccessor('key');
$accessor->setValue($object, 'value');

echo $accessor->getValue($object);
// 'value'
```
