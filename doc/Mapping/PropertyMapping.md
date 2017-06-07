# PropertyMapping

```php
<?php

use Chubbyphp\Deserialization\Deserializer\PropertyDeserializerInterface;
use Chubbyphp\Deserialization\Mapping\PropertyMapping;

$propertyDeserializer = new class implements PropertyDeserializerInterface
{
    /**
     * @param string                     $path
     * @param mixed                      $serializedValue
     * @param mixed                      $existingValue
     * @param object                     $object
     * @param DeserializerInterface|null $deserializer
     *
     * @return mixed
     */
    public function deserializeProperty(
        string $path,
        $serializedValue,
        $existingValue = null,
        $object = null,
        DeserializerInterface $deserializer = null
    ) {
        return $serializedValue;
    }
};

$propertyMapping = new PropertyMapping('name', $propertyDeserializer);
$propertyMapping->getName(); // 'name'
$propertyMapping->getPropertyDeserializer(); // implements PropertyDeserializerInterface
```
