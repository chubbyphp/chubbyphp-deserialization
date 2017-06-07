# ObjectMappingRegistry

```php
<?php

use Chubbyphp\Deserialization\Registry\ObjectMappingRegistry;
use MyProject\Model\Model;
use MyProject\Deserialization\ModelMapping;

$objectMappingRegistry = new ObjectMappingRegistry([new ModelMapping]);
$objectMappingRegistry->getObjectMappingForClass(Model::class); // new ModelMapping()
```
