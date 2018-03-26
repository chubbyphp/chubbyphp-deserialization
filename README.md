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
 * psr/http-message: ~1.0
 * psr/log: ~1.0

## Suggest

 * container-interop/container-interop: ~1.0
 * pimple/pimple: ~3.0
 * symfony/yaml: ~2.7|~3.0 (application/x-yaml support)

## Installation

Through [Composer](http://getcomposer.org) as [chubbyphp/chubbyphp-deserialization][1].

```sh
composer require chubbyphp/chubbyphp-deserialization "~2.1@dev"
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
 * [DateFieldDenormalizer][11]
 * [FieldDenormalizer][12]
 
##### Relation Field Denormalizer

###### Basic

 * [EmbedManyFieldDenormalizer][13]
 * [EmbedOneFieldDenormalizer][14]
 * [ReferenceManyFieldDenormalizer][15]
 * [ReferenceOneFieldDenormalizer][16]

###### Doctrine

 * [EmbedManyFieldDenormalizer][17]
 * [EmbedOneFieldDenormalizer][18]
 * [ReferenceManyFieldDenormalizer][19]
 * [ReferenceOneFieldDenormalizer][20]

#### Denormalizer Context

 * [DenormalizerContext][21]
 * [DenormalizerContextBuilder][22]

### DenormalizerObjectMappingRegistry

* [DenormalizerObjectMappingRegistry][23]

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

 * [DenormalizationFieldMapping][24]
 * [DenormalizationFieldMappingBuilder][25]

#### DenormalizationObjectMapping

 * [AdvancedDenormalizationObjectMapping][26]
 * [SimpleDenormalizationObjectMapping][27]

#### LazyDenormalizationObjectMapping

 * [LazyDenormalizationObjectMapping][28]

### Provider

* [DeserializationProvider][29]

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
[11]: doc/Denormalizer/DateFieldDenormalizer.md
[12]: doc/Denormalizer/FieldDenormalizer.md

[13]: doc/Denormalizer/Relation/Basic/EmbedManyFieldDenormalizer.md
[14]: doc/Denormalizer/Relation/Basic/EmbedOneFieldDenormalizer.md
[15]: doc/Denormalizer/Relation/Basic/ReferenceManyFieldDenormalizer.md
[16]: doc/Denormalizer/Relation/Basic/ReferenceOneFieldDenormalizer.md

[17]: doc/Denormalizer/Relation/Doctrine/EmbedManyFieldDenormalizer.md
[18]: doc/Denormalizer/Relation/Doctrine/EmbedOneFieldDenormalizer.md
[19]: doc/Denormalizer/Relation/Doctrine/ReferenceManyFieldDenormalizer.md
[20]: doc/Denormalizer/Relation/Doctrine/ReferenceOneFieldDenormalizer.md

[21]: doc/Denormalizer/DenormalizerContext.md
[22]: doc/Denormalizer/DenormalizerContextBuilder.md

[23]: doc/Denormalizer/DenormalizerObjectMappingRegistry.md

[24]: doc/Mapping/DenormalizationFieldMapping.md
[25]: doc/Mapping/DenormalizationFieldMappingBuilder.md

[26]: doc/Mapping/AdvancedDenormalizationObjectMapping.md
[27]: doc/Mapping/SimpleDenormalizationObjectMapping.md

[28]: doc/Mapping/LazyDenormalizationObjectMapping.md

[29]: doc/Provider/DeserializationProvider.md
