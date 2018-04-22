# PropertyModelReferenceDeserializer

```php
<?php

use Chubbyphp\Model\Reference\ModelReference;
use Chubbyphp\Deserialization\Deserializer;
use Chubbyphp\DeserializationModel\Deserializer\PropertyModelReferenceDeserializer;
use MyProject\Model\Model;

$deserializer = new Deserializer(...);

$resolver = ...;

$propertyDeserializer = new PropertyModelReferenceDeserializer($resolver, Model::class);

// deserialize by array
$property = $propertyDeserializer->deserializeProperty(
    'path',
    ['name' => 'name1'],
    new ModelReference(...),
    null,
    $deserializer
);

// deserialize by id
$property = $propertyDeserializer->deserializeProperty(
    'path',
    'id1',
    new ModelReference(...),
    null,
    $deserializer
);
```
