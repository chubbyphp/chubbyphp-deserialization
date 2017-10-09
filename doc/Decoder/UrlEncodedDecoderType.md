# UrlEncodedDecoderType

```php
<?php

use Chubbyphp\Deserialization\Decoder\UrlEncodedDecoderType;

$transformer = new UrlEncodedDecoderType();

echo $transformer->getContentType(); // 'application/x-www-form-urlencoded'

print_r($transformer->decode('name=php')); // ['name' => 'php']
```
