# XmlTransformer

```php
<?php

use Chubbyphp\Deserialization\Transformer\XmlTransformer;

$transformer = new XmlTransformer();
$transformer->getContentType(); // 'application/xml'
$transformer->transform($xml); // transforms xml to an array
```
