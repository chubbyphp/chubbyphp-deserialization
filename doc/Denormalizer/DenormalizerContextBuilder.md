# DenormalizerContextBuilder

```php
<?php

use Chubbyphp\Deserialization\Denormalizer\DenormalizerContextBuilder;

$context = new DenormalizerContextBuilder::create()
    ->setAllowedAdditionalFields(true)
    ->setGroups(['group1'])
    ->getContext();

echo $context->isAllowedAdditionalFields();
// true

print_r($context->getGroups());
// ['group1']
```
