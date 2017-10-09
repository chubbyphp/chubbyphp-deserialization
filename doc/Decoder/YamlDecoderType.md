# YamlDecoderType

```php
<?php

use Chubbyphp\Deserialization\Decoder\YamlDecoderType;

$transformer = new YamlDecoderType();

echo $transformer->getContentType(); // 'application/x-yaml'

print_r($transformer->decode('name: php')); // ['name' => 'php']
```
