# XmlTypeDecoder

```php
<?php

use Chubbyphp\Deserialization\Decoder\XmlTypeDecoder;

$decoderType = new XmlTypeDecoder();

echo $decoderType->getContentType();
// 'application/xml'

print_r($decoderType->decode('<name type="string">php</name>'));
// ['name' => 'php']
```
