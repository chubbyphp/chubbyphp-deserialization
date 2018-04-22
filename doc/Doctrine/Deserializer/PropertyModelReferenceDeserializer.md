# PropertyModelReferenceDeserializer

```php
<?php

use Chubbyphp\Deserialization\Deserializer;
use Chubbyphp\DeserializationDoctrine\Deserializer\PropertyModelReferenceDeserializer;
use MyProject\Model\Model;

$deserializer = new Deserializer(...);

$resolver = ...;

$propertyDeserializer = new PropertyModelReferenceDeserializer($resolver, Model::class);

// deserialize by array
$property = $propertyDeserializer->deserializeProperty(
    'path',
    ['name' => 'name1'],
    null,
    null,
    $deserializer
);

// deserialize by id
$property = $propertyDeserializer->deserializeProperty(
    'path',
    'id1',
    null,
    null,
    $deserializer
);
```
