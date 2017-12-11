# DenormalizerContext

```php
<?php

use Chubbyphp\Deserialization\Denormalizer\DenormalizerContext;
use Psr\Http\Message\ServerRequestInterface;

$request = ...;

$context = new DenormalizerContext(['allowed_additional_field'], ['group1'], $request);

echo $context->getAllowedAdditionalFields();
// ['allowed_additional_field']

print_r($context->getGroups());
// ['group1']

$request = $context->getRequest();
```
