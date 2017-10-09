# XmlDecoderType

```php
<?php

use Chubbyphp\Deserialization\Decoder\XmlDecoderType;

$transformer = new XmlDecoderType();
$transformer->getContentType(); // 'application/xml'
$transformer->decode('<name type="string">php</name>'); // ['name' => 'php']
```
