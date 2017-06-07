# PropertyMapping

```php
<?php

use Chubbyphp\Deserialization\Deserializer\PropertyDeserializerInterface;
use Chubbyphp\Deserialization\Mapping\PropertyMapping;

$propertyDeserializer = new class implements PropertyDeserializerInterface {
    public function deserializeProperty(
        string $path,
        $serializedValue,
        $existingValue = null,
        $object = null,
        DeserializerInterface $deserializer = null
    )
};

$propertyMapping = new PropertyMapping('name', $propertyDeserializer);
$propertyMapping->getName(); // 'name'
$propertyMapping->getPropertyDeserializer(); // implements PropertyDeserializerInterface
```
