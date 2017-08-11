# YamlTransformer

```php
<?php

use Chubbyphp\Deserialization\Transformer\YamlTransformer;

$transformer = new YamlTransformer();
$transformer->getContentType(); // 'application/x-yaml'
$transformer->transform($yaml); // transforms yaml to an array
```
