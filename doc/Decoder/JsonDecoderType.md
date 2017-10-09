# JsonDecoderType

```php
<?php

use Chubbyphp\Deserialization\Decoder\JsonDecoderType;

$transformer = new JsonDecoderType();

echo $transformer->getContentType(); // 'application/json'

print_r($transformer->decode('{"name": "php"}')); // ['name' => 'php']
```
