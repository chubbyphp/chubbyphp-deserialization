# DenormalizerContext

```php
<?php

use Chubbyphp\Deserialization\Denormalizer\DenormalizerContext;

$context = new DenormalizerContext(true, ['group1']);

echo $context->isAllowedAdditionalFields();
// true

print_r($context->getGroups());
// ['group1']
```
