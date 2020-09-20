# XmlTypeDecoder (alias for Jsonx)

```php
<?php

use Chubbyphp\Deserialization\Decoder\XmlTypeDecoder;

$decoderType = new XmlTypeDecoder();

echo $decoderType->getContentType();
// 'application/xml'

print_r($decoderType->decode('<json:object><json:string name="name">php</json:string></json:object>'));
// ['name' => 'php']
```
