# YamlDecoderType

```php
<?php

use Chubbyphp\Deserialization\Decoder\YamlDecoderType;

$transformer = new YamlDecoderType();
$transformer->getContentType(); // 'application/x-yaml'
$transformer->decode('name: php'); // ['name' => 'php']
```
