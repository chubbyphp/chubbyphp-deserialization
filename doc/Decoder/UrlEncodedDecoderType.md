# UrlEncodedDecoderType

```php
<?php

use Chubbyphp\Deserialization\Decoder\UrlEncodedDecoderType;

$transformer = new UrlEncodedDecoderType();
$transformer->getContentType(); // 'application/x-www-form-urlencoded'
$transformer->decode('name=php'); // ['name' => 'php']
```
