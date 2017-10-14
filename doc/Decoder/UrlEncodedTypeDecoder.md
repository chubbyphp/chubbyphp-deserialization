# UrlEncodedTypeDecoder

```php
<?php

use Chubbyphp\Deserialization\Decoder\UrlEncodedTypeDecoder;

$decoderType = new UrlEncodedTypeDecoder();

echo $decoderType->getContentType(); // 'application/x-www-form-urlencoded'

print_r($decoderType->decode('name=php')); // ['name' => 'php']
```
