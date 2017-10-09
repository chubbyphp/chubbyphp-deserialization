# XmlDecoderType

```php
<?php

use Chubbyphp\Deserialization\Decoder\XmlDecoderType;

$transformer = new XmlDecoderType();
$transformer->getContentType(); // 'application/xml'
$transformer->decode('<key type="string">value</key>'); // ['key' => 'value']
```
