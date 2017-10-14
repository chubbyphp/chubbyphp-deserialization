# PropertyAccessor

```php
<?php

use Chubbyphp\Deserialization\Accessor\PropertyAccessor;

$accessor = new PropertyAccessor('name');
$accessor->setValue($object, 'php');

echo $accessor->getValue($object);
// 'php'
```
