# JsonDecoderType

```php
<?php

use Chubbyphp\Deserialization\Decoder\JsonDecoderType;

$transformer = new JsonDecoderType();
$transformer->getContentType(); // 'application/json'
$transformer->decode('{"name": "php"}'); // ['name' => 'php']
```
