# JsonDecoderType

```php
<?php

use Chubbyphp\Deserialization\Decoder\JsonDecoderType;

$transformer = new JsonDecoderType();
$transformer->getContentType(); // 'application/json'
$transformer->decode('{"key": "value"}'); // ['key' => 'value']
```
