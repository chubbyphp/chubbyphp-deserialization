# YamlDecoderType

```php
<?php

use Chubbyphp\Deserialization\Decoder\YamlDecoderType;

$transformer = new YamlDecoderType();
$transformer->getContentType(); // 'application/x-yaml'
$transformer->decode('key: value'); // ['key' => 'value']
```
