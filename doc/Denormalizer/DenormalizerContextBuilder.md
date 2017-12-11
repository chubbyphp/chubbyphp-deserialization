# DenormalizerContextBuilder

```php
<?php

use Chubbyphp\Deserialization\Denormalizer\DenormalizerContextBuilder;
use Psr\Http\Message\ServerRequestInterface;

$request = ...;

$context = DenormalizerContextBuilder::create()
    ->setAllowedAdditionalFields(['allowed_additional_field'])
    ->setGroups(['group1'])
    ->setRequest($request)
    ->getContext();

echo $context->getAllowedAdditionalFields();
// ['allowed_additional_field']

print_r($context->getGroups());
// ['group1']

$request = $context->getRequest();
```
