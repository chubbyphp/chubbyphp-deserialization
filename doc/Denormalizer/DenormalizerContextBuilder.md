# DenormalizerContextBuilder

```php
<?php

use Chubbyphp\Deserialization\Denormalizer\DenormalizerContextBuilder;
use Psr\Http\Message\ServerRequestInterface;

/** @var ServerRequestInterface $request */
$request = ...;

$context = DenormalizerContextBuilder::create()
    ->setAllowedAdditionalFields(['allowed_additional_field'])
    ->setGroups(['group1'])
    ->setRequest($request)
    ->setResetMissingFields(true) // deprecated
    ->setClearMissing(true)
    ->getContext();

echo $context->getAllowedAdditionalFields();
// ['allowed_additional_field']

print_r($context->getGroups());
// ['group1']

$context->getRequest();
// instanceof ServerRequestInterface

echo $context->isResetMissingFields();
// true

echo $context->isClearMissing();
// true

$context->setAttributes(['key' => 'value']);
```
