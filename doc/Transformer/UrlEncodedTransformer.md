# UrlEncodedTransformer

```php
<?php

use Chubbyphp\Deserialization\Transformer\UrlEncodedTransformer;

$transformer = new UrlEncodedTransformer();
$transformer->getContentType(); // 'application/x-www-form-urlencoded'
$transformer->transform($urlEncoded); // transforms urlencoded string to an array
```
