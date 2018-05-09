# DenormalizationFieldMappingBuilder

```php
<?php

use Chubbyphp\Deserialization\Accessor\PropertyAccessor;
use Chubbyphp\Deserialization\Denormalizer\FieldDenormalizer;
use Chubbyphp\Deserialization\Mapping\DenormalizationFieldMappingBuilder;
use Chubbyphp\Deserialization\Mapping\DenormalizationFieldMappingInterface;

$fieldMapping = DenormalizationFieldMappingBuilder::create('name')
    ->setGroups(['group1'])
    ->setFieldDenormalizer(
        new FieldDenormalizer(
            new PropertyAccessor('name')
        )
    )
    ->setForceType(DenormalizationFieldMappingInterface::FORCETYPE_INT)
    ->getMapping();

echo $fieldMapping->getName();
// 'name'

print_r($fieldMapping->getGroups());
// ['group1']

$fieldMapping
    ->getFieldDenormalizer()
    ->denormalizeField(...);

print_r($fieldMapping->getForceType());
// 'integer'
```
