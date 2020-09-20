# DenormalizerContext

```php
<?php

use Chubbyphp\Deserialization\Denormalizer\DenormalizerContext;
use Psr\Http\Message\ServerRequestInterface;

/** @var ServerRequestInterface $request */
$request = ...;

$context = new DenormalizerContext($request, ['key' => 'value'], ['allowed_additional_field'], true);

$context->getRequest();
// instanceof ServerRequestInterface

$context->getAttributes();
$context->getAttribute('name');
$context = $context->withAttribute('name');

echo $context->getAllowedAdditionalFields();
// ['allowed_additional_field']

echo $context->isClearMissing();
// true
```
