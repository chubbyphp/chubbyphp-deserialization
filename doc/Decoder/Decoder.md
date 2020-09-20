# Decoder

```php
<?php

use Chubbyphp\Deserialization\Decoder\Decoder;
use Chubbyphp\Deserialization\Decoder\JsonTypeDecoder;
use Chubbyphp\Deserialization\Decoder\JsonxTypeDecoder;
use Chubbyphp\Deserialization\Decoder\UrlEncodedTypeDecoder;
use Chubbyphp\Deserialization\Decoder\XmlTypeDecoder;
use Chubbyphp\Deserialization\Decoder\YamlTypeDecoder;

$decoder = new Decoder([
    new JsonTypeDecoder(),
    new JsonxTypeDecoder(),
    new UrlEncodedTypeDecoder(),
    new XmlTypeDecoder(),
    new YamlTypeDecoder()
]);

print_r($decoder->getContentTypes());
//[
//    'application/json',
//    'application/jsonx+xml',
//    'application/x-www-form-urlencoded',
//    'application/xml',
//    'application/x-yaml'
//]

print_r($decoder->decode(
    '{"name": "php"}',
    'application/json'
));
// ['name' => 'php']
```
