# DenormalizerObjectMappingRegistry

```php
<?php

use Chubbyphp\Deserialization\Denormalizer\DenormalizerObjectMappingRegistry;

$registry = new DenormalizerObjectMappingRegistry([]);

echo $registry->getObjectMapping('class')->getClass();
// 'class'
```
