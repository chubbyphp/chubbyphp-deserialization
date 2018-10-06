# DenormalizerContext

```php
<?php

use Chubbyphp\Deserialization\Denormalizer\DenormalizerContext;
use Psr\Http\Message\ServerRequestInterface;

/** @var ServerRequestInterface $request */
$request = ...;

$context = new DenormalizerContext(['allowed_additional_field'], ['group1'], $request, true);

echo $context->getAllowedAdditionalFields();
// ['allowed_additional_field']

print_r($context->getGroups());
// ['group1']

$context->getRequest();
// instanceof ServerRequestInterface

echo $context->isResetMissingFields();
// true
```
