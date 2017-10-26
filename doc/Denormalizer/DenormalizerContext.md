# DenormalizerContext

```php
<?php

use Chubbyphp\Deserialization\Denormalizer\DenormalizerContext;
use Psr\Http\Message\ServerRequestInterface;

$request = ...;

$context = new DenormalizerContext(true, ['group1'], $request);

echo $context->isAllowedAdditionalFields();
// true

print_r($context->getGroups());
// ['group1']

$request = $context->getRequest();
```
