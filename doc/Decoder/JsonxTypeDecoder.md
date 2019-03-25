# JsonxTypeDecoder

```php
<?php

use Chubbyphp\Deserialization\Decoder\JsonxTypeDecoder;

$decoderType = new JsonxTypeDecoder();

echo $decoderType->getContentType();
// 'application/x-jsonx'

print_r($decoderType->decode('<json:object><json:string name="name">php</json:string></json:object>'));
// ['name' => 'php']
```
