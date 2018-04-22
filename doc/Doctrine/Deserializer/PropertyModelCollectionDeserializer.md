# PropertyModelCollectionDeserializer

```php
<?php

use Chubbyphp\Deserialization\Deserializer;
use Chubbyphp\DeserializationDoctrine\Deserializer\PropertyModelCollectionDeserializer;
use Doctrine\Common\Collections\ArrayCollection;
use MyProject\Model\Model;

$deserializer = new Deserializer(...);

$propertyDeserializer = new PropertyModelCollectionDeserializer(Model::class);

$property = $propertyDeserializer->deserializeProperty(
    'path',
    [['name' => 'name1'], ['name' => 'name2']],
    new ArrayCollection(...),
    null,
    $deserializer
);

```
