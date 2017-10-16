# chubbyphp-deserialization

[![Build Status](https://api.travis-ci.org/chubbyphp/chubbyphp-deserialization.png?branch=master)](https://travis-ci.org/chubbyphp/chubbyphp-deserialization)
[![Total Downloads](https://poser.pugx.org/chubbyphp/chubbyphp-deserialization/downloads.png)](https://packagist.org/packages/chubbyphp/chubbyphp-deserialization)
[![Latest Stable Version](https://poser.pugx.org/chubbyphp/chubbyphp-deserialization/v/stable.png)](https://packagist.org/packages/chubbyphp/chubbyphp-deserialization)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/chubbyphp/chubbyphp-deserialization/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/chubbyphp/chubbyphp-deserialization/?branch=master)
[![Code Coverage](https://scrutinizer-ci.com/g/chubbyphp/chubbyphp-deserialization/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/chubbyphp/chubbyphp-deserialization/?branch=master)

## Description

A simple deserialization.

## Requirements

 * php: ~7.0
 * psr/log: ~1.0

## Suggest

 * container-interop/container-interop: ~1.0
 * pimple/pimple: ~3.0
 * symfony/yaml: ~2.7|~3.0 (application/x-yaml support)

## Installation

Through [Composer](http://getcomposer.org) as [chubbyphp/chubbyphp-deserialization][1].

```sh
composer require chubbyphp/chubbyphp-deserialization "~2.0@alpha"
```

## Usage

### Accessor

 * [MethodAccessor][2]
 * [PropertyAccessor][3]

### Decoder

```php
<?php

use Chubbyphp\Deserialization\Decoder\Decoder;
use Chubbyphp\Deserialization\Decoder\JsonTypeDecoder;
use Chubbyphp\Deserialization\Decoder\UrlEncodedTypeDecoder;
use Chubbyphp\Deserialization\Decoder\XmlTypeDecoder;
use Chubbyphp\Deserialization\Decoder\YamlTypeDecoder;

$decoder = new Decoder([
    new JsonTypeDecoder(),
    new UrlEncodedTypeDecoder(),
    new XmlTypeDecoder(),
    new YamlTypeDecoder()
]);

print_r($decoder->getContentTypes());
//[
//    'application/json',
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

#### Type Decoder

 * [JsonTypeDecoder][4]
 * [UrlEncodedTypeDecoder][5]
 * [XmlTypeDecoder][6]
 * [YamlTypeDecoder][7]

### Denormalizer

```php
<?php

use Chubbyphp\Deserialization\Denormalizer\Denormalizer;
use Chubbyphp\Deserialization\Denormalizer\DenormalizerObjectMappingRegistry;
use MyProject\Deserialization\ModelMapping;
use MyProject\Model\Model;

$logger = ...;

$denormalizer = new Denormalizer(
    new DenormalizerObjectMappingRegistry([new ModelMapping()]),
    $logger
);

$model = $denormalizer->denormalize(
    Model::class,
    ['name' => 'php']
);

echo $model->getName();
// 'php'
```

#### Field Denormalizer

 * [CallbackFieldDenormalizer][8]
 * [CollectionFieldDenormalizer][9]
 * [DateFieldDenormalizer][10]
 * [FieldDenormalizer][11]

#### Denormalizer Context

 * [DenormalizerContext][12]
 * [DenormalizerContextBuilder][13]


### DenormalizerObjectMappingRegistry

* [DenormalizerObjectMappingRegistry][14]

### Deserializer

```php
<?php

use Chubbyphp\Deserialization\Decoder\Decoder;
use Chubbyphp\Deserialization\Decoder\JsonTypeDecoder;
use Chubbyphp\Deserialization\Decoder\UrlEncodedTypeDecoder;
use Chubbyphp\Deserialization\Decoder\XmlTypeDecoder;
use Chubbyphp\Deserialization\Decoder\YamlTypeDecoder;
use Chubbyphp\Deserialization\Denormalizer\Denormalizer;
use Chubbyphp\Deserialization\Denormalizer\DenormalizerObjectMappingRegistry;
use Chubbyphp\Deserialization\Deserializer;
use MyProject\Deserialization\ModelMapping;
use MyProject\Model\Model;

$logger = ...;

$deserializer = new Deserializer(
    new Decoder([
        new JsonTypeDecoder(),
        new UrlEncodedTypeDecoder(),
        new XmlTypeDecoder(),
        new YamlTypeDecoder()
    ]),
    new Denormalizer(new DenormalizerObjectMappingRegistry([
        new ModelMapping()
    ]), $logger)
);

$model = $deserializer->deserialize(
    Model::class,
    '{"name": "php"}',
    'application/json'
);

echo $model->getName();
// 'php'
```

### Mapping

#### DenormalizationFieldMapping

 * [DenormalizationFieldMapping][15]
 * [DenormalizationFieldMappingBuilder][16]

#### DenormalizationObjectMapping

 * [AdvancedDenormalizationObjectMapping][17]
 * [SimpleDenormalizationObjectMapping][18]

#### LazyDenormalizationObjectMapping

 * [LazyDenormalizationObjectMapping][19]

### Provider

* [DeserializationProvider][20]

## Copyright

Dominik Zogg 2017


[1]: https://packagist.org/packages/chubbyphp/chubbyphp-deserialization

[2]: doc/Accessor/MethodAccessor.md
[3]: doc/Accessor/PropertyAccessor.md

[4]: doc/Decoder/JsonTypeDecoder.md
[5]: doc/Decoder/UrlEncodedTypeDecoder.md
[6]: doc/Decoder/XmlTypeDecoder.md
[7]: doc/Decoder/YamlTypeDecoder.md

[8]: doc/Denormalizer/CallbackFieldDenormalizer.md
[9]: doc/Denormalizer/CollectionFieldDenormalizer.md
[10]: doc/Denormalizer/DateFieldDenormalizer.md
[11]: doc/Denormalizer/FieldDenormalizer.md

[12]: doc/Denormalizer/DenormalizerContext.md
[13]: doc/Denormalizer/DenormalizerContextBuilder.md

[14]: doc/Denormalizer/DenormalizerObjectMappingRegistry.md

[15]: doc/Mapping/DenormalizationFieldMapping.md
[16]: doc/Mapping/DenormalizationFieldMappingBuilder.md

[17]: doc/Mapping/AdvancedDenormalizationObjectMapping.md
[18]: doc/Mapping/SimpleDenormalizationObjectMapping.md

[19]: doc/Mapping/LazyDenormalizationObjectMapping.md

[20]: doc/Provider/DeserializationProvider.md
