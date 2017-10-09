# PropertyAccessor

```php
<?php

use Chubbyphp\Deserialization\Accessor\PropertyAccessor;

$accessor = new PropertyAccessor('key');
$accessor->setValue($object, 'value');
$accessor->getValue($object); // 'value'
```
