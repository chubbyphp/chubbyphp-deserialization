# chubbyphp-deserialization

[![Build Status](https://api.travis-ci.org/chubbyphp/chubbyphp-deserialization.png?branch=master)](https://travis-ci.org/chubbyphp/chubbyphp-deserialization)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/chubbyphp/chubbyphp-deserialization/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/chubbyphp/chubbyphp-deserialization/?branch=master)
[![Code Coverage](https://scrutinizer-ci.com/g/chubbyphp/chubbyphp-deserialization/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/chubbyphp/chubbyphp-deserialization/?branch=master)
[![Total Downloads](https://poser.pugx.org/chubbyphp/chubbyphp-deserialization/downloads.png)](https://packagist.org/packages/chubbyphp/chubbyphp-deserialization)
[![Monthly Downloads](https://poser.pugx.org/chubbyphp/chubbyphp-deserialization/d/monthly)](https://packagist.org/packages/chubbyphp/chubbyphp-deserialization)
[![Latest Stable Version](https://poser.pugx.org/chubbyphp/chubbyphp-deserialization/v/stable.png)](https://packagist.org/packages/chubbyphp/chubbyphp-deserialization)
[![Latest Unstable Version](https://poser.pugx.org/chubbyphp/chubbyphp-deserialization/v/unstable)](https://packagist.org/packages/chubbyphp/chubbyphp-deserialization)

## Description

A simple deserialization.

## Requirements

 * php: ~7.0
 * psr/http-message: ~1.0
 * psr/log: ~1.0

## Suggest

 * container-interop/container-interop: ~1.0
 * pimple/pimple: ~3.0
 * symfony/yaml: ~2.7|~3.0|~4.0 (application/x-yaml support)

## Installation

Through [Composer](http://getcomposer.org) as [chubbyphp/chubbyphp-deserialization][1].

```sh
composer require chubbyphp/chubbyphp-deserialization "~2.3"
```

## Usage

### Accessor

 * [MethodAccessor][2]
 * [PropertyAccessor][3]

### Decoder

 * [Decoder][4]

#### Type Decoder

 * [JsonTypeDecoder][5]
 * [UrlEncodedTypeDecoder][6]
 * [XmlTypeDecoder][7]
 * [YamlTypeDecoder][8]

### Denormalizer

 * [Denormalizer][9]

#### Field Denormalizer

 * [CallbackFieldDenormalizer][10]
 * [DateTimeFieldDenormalizer][11]
 * [FieldDenormalizer][12]
 * [ConvertTypeFieldDenormalizer][13]
 
##### Relation Field Denormalizer

 * [EmbedManyFieldDenormalizer][14]
 * [EmbedOneFieldDenormalizer][15]
 * [ReferenceManyFieldDenormalizer][16]
 * [ReferenceOneFieldDenormalizer][17]

#### Denormalizer Context

 * [DenormalizerContext][18]
 * [DenormalizerContextBuilder][19]

### DenormalizerObjectMappingRegistry

* [DenormalizerObjectMappingRegistry][20]

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
    new Denormalizer(
        new DenormalizerObjectMappingRegistry([
            new ModelMapping()
        ]),
        $logger
    )
);

$model = $deserializer->deserialize(
    Model::class,
    '{"name": "php"}',
    'application/json'
);

echo $model->getName();
// 'php'

print_r($deserializer->getContentTypes());
//[
//    'application/json',
//    'application/x-www-form-urlencoded',
//    'application/xml',
//    'application/x-yaml'
//]

print_r($deserializer->decode(
    '{"name": "php"}',
    'application/json'
));
// ['name' => 'php']

$model = $denormalizer->denormalize(
    Model::class,
    ['name' => 'php']
);

echo $model->getName();
// 'php'
```

### Mapping

#### DenormalizationFieldMapping

 * [DenormalizationFieldMapping][21]
 * [DenormalizationFieldMappingBuilder][22]

#### DenormalizationObjectMapping

 * [AdvancedDenormalizationObjectMapping][23]
 * [SimpleDenormalizationObjectMapping][24]

#### LazyDenormalizationObjectMapping

 * [LazyDenormalizationObjectMapping][25]

### Provider

* [DeserializationProvider][26]

## Copyright

Dominik Zogg 2017


[1]: https://packagist.org/packages/chubbyphp/chubbyphp-deserialization

[2]: doc/Accessor/MethodAccessor.md
[3]: doc/Accessor/PropertyAccessor.md

[4]: doc/Decoder/Decoder.md

[5]: doc/Decoder/JsonTypeDecoder.md
[6]: doc/Decoder/UrlEncodedTypeDecoder.md
[7]: doc/Decoder/XmlTypeDecoder.md
[8]: doc/Decoder/YamlTypeDecoder.md

[9]: doc/Denormalizer/Denormalizer.md

[10]: doc/Denormalizer/CallbackFieldDenormalizer.md
[11]: doc/Denormalizer/DateTimeFieldDenormalizer.md
[12]: doc/Denormalizer/FieldDenormalizer.md
[13]: doc/Denormalizer/ConvertTypeFieldDenormalizer.md

[14]: doc/Denormalizer/Relation/EmbedManyFieldDenormalizer.md
[15]: doc/Denormalizer/Relation/EmbedOneFieldDenormalizer.md
[16]: doc/Denormalizer/Relation/ReferenceManyFieldDenormalizer.md
[17]: doc/Denormalizer/Relation/ReferenceOneFieldDenormalizer.md

[18]: doc/Denormalizer/DenormalizerContext.md
[19]: doc/Denormalizer/DenormalizerContextBuilder.md

[20]: doc/Denormalizer/DenormalizerObjectMappingRegistry.md

[21]: doc/Mapping/DenormalizationFieldMapping.md
[22]: doc/Mapping/DenormalizationFieldMappingBuilder.md

[23]: doc/Mapping/AdvancedDenormalizationObjectMapping.md
[24]: doc/Mapping/SimpleDenormalizationObjectMapping.md

[25]: doc/Mapping/LazyDenormalizationObjectMapping.md

[26]: doc/Provider/DeserializationProvider.md
