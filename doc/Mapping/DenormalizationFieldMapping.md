# DenormalizationFieldMapping

```php
<?php

use Chubbyphp\Deserialization\Accessor\PropertyAccessor;
use Chubbyphp\Deserialization\Denormalizer\FieldDenormalizer;
use Chubbyphp\Deserialization\Mapping\DenormalizationFieldMapping;

$fieldMapping = new DenormalizationFieldMapping(
    'name',
    ['group1'],
    new FieldDenormalizer(
        new PropertyAccessor('name')
    )
);

echo $fieldMapping->getName();
// 'name'

print_r($fieldMapping->getGroups());
// ['group1']

$fieldMapping
    ->getFieldDenormalizer()
    ->denormalizeField(...);
```
