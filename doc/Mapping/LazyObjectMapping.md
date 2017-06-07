# LazyObjectMapping

```php
<?php

use Chubbyphp\Deserialization\LazyObjectMapping;
use MyProject\Model\Model;
use MyProject\Deserialization\ModelMapping;

$container[ModelMapping::class] = function () use ($container) {
    return new ModelMapping();
};

$lazyObjectMapping = new LazyObjectMapping($container, ModelMapping::class, Model::class);
```
