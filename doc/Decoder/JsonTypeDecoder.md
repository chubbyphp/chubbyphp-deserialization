# JsonTypeDecoder

```php
<?php

use Chubbyphp\Deserialization\Decoder\JsonTypeDecoder;

$decoderType = new JsonTypeDecoder();

echo $decoderType->getContentType();
// 'application/json'

print_r($decoderType->decode('{"name": "php"}'));
// ['name' => 'php']
```
