# DenormalizerContextBuilder

```php
<?php

use Chubbyphp\Deserialization\Denormalizer\DenormalizerContextBuilder;
use Psr\Http\Message\ServerRequestInterface;

/** @var ServerRequestInterface $request */
$request = ...;

$context = DenormalizerContextBuilder::create()
    ->setRequest($request)
    ->setAttributes(['key' => 'value'])
    ->setAllowedAdditionalFields(['allowed_additional_field'])
    ->setClearMissing(true)
    ->getContext();

echo $context->getAllowedAdditionalFields();
// ['allowed_additional_field']

$context->getRequest();
// instanceof ServerRequestInterface

echo $context->isClearMissing();
// true

echo $context->getAttributes('key');
// value
```
