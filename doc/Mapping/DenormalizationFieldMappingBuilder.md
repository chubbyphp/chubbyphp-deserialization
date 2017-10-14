# DenormalizationFieldMappingBuilder

```php
<?php

use Chubbyphp\Deserialization\Accessor\PropertyAccessor;
use Chubbyphp\Deserialization\Denormalizer\FieldDenormalizer;
use Chubbyphp\Deserialization\Mapping\DenormalizationFieldMappingBuilder;

$fieldMapping = new DenormalizationFieldMappingBuilder('name')
    ->setGroups(['group1'])
    ->setFieldDenormalizer(
        new FieldDenormalizer(
            new PropertyAccessor('name')
        )
    )
    ->getMapping();

echo $fieldMapping->getName();
// 'name'

print_r($fieldMapping->getGroups());
// ['group1']

$fieldMapping->getFieldDenormalizer()->denormalizeField(...);
```
