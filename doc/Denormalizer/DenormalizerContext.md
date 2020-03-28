# DenormalizerContext

```php
<?php

use Chubbyphp\Deserialization\Denormalizer\DenormalizerContext;
use Psr\Http\Message\ServerRequestInterface;

/** @var ServerRequestInterface $request */
$request = ...;

$context = new DenormalizerContext(['allowed_additional_field'], ['group1'], $request, true, [], true);

echo $context->getAllowedAdditionalFields();
// ['allowed_additional_field']

print_r($context->getGroups());
// ['group1']

$context->getRequest();
// instanceof ServerRequestInterface

echo $context->isResetMissingFields();
// true

echo $context->isClearMissing();

$context->getAttributes();
$context->getAttribute('name');
$context = $context->withAttribute('name');
```
