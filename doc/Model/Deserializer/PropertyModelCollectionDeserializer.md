# PropertyModelCollectionDeserializer

```php
<?php

use Chubbyphp\Model\Collection\ModelCollection;
use Chubbyphp\Deserialization\Deserializer;
use Chubbyphp\DeserializationModel\Deserializer\PropertyModelCollectionDeserializer;
use MyProject\Model\Model;

$deserializer = new Deserializer(...);

$propertyDeserializer = new PropertyModelCollectionDeserializer(Model::class);

$property = $propertyDeserializer->deserializeProperty(
    'path',
    [['name' => 'name1'], ['name' => 'name2']],
    new ModelCollection(...),
    null,
    $deserializer
);

```
