# JsonDecoderType

```php
<?php

use Chubbyphp\Deserialization\Decoder\JsonDecoderType;

$transformer = new JsonDecoderType();
$transformer->getContentType(); // 'application/json'

print_r($transformer->decode('{"name": "php"}')); // ['name' => 'php']
```
