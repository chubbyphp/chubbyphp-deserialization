# DenormalizerContextBuilder

```php
<?php

use Chubbyphp\Deserialization\Denormalizer\DenormalizerContextBuilder;
use Psr\Http\Message\ServerRequestInterface;

$request = ...;

$context = DenormalizerContextBuilder::create()
    ->setAllowedAdditionalFields(true)
    ->setGroups(['group1'])
    ->setRequest($request)
    ->getContext();

echo $context->isAllowedAdditionalFields();
// true

print_r($context->getGroups());
// ['group1']

$request = $context->getRequest();
```
