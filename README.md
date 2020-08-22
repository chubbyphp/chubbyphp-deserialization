# chubbyphp-deserialization

[![Build Status](https://api.travis-ci.org/chubbyphp/chubbyphp-deserialization.png?branch=master)](https://travis-ci.org/chubbyphp/chubbyphp-deserialization)
[![Coverage Status](https://coveralls.io/repos/github/chubbyphp/chubbyphp-deserialization/badge.svg?branch=master)](https://coveralls.io/github/chubbyphp/chubbyphp-deserialization?branch=master)
[![Latest Stable Version](https://poser.pugx.org/chubbyphp/chubbyphp-deserialization/v/stable.png)](https://packagist.org/packages/chubbyphp/chubbyphp-deserialization)
[![Total Downloads](https://poser.pugx.org/chubbyphp/chubbyphp-deserialization/downloads.png)](https://packagist.org/packages/chubbyphp/chubbyphp-deserialization)
[![Monthly Downloads](https://poser.pugx.org/chubbyphp/chubbyphp-deserialization/d/monthly)](https://packagist.org/packages/chubbyphp/chubbyphp-deserialization)
[![Daily Downloads](https://poser.pugx.org/chubbyphp/chubbyphp-deserialization/d/daily)](https://packagist.org/packages/chubbyphp/chubbyphp-deserialization)

## Description

A simple deserialization.

## Requirements

 * php: ^7.2
 * psr/http-message: ^1.0.1
 * psr/log: ^1.1

## Suggest

 * chubbyphp/chubbyphp-container: ^1.0
 * pimple/pimple: ^3.2.3
 * psr/container: ^1.0
 * symfony/dependency-injection: ^2.8.50|^3.4.26|^4.2.7|^5.0 (symfony integration)
 * symfony/yaml: ^2.8.50|^3.4.26|^4.2.7|^5.0 (application/x-yaml support)

## Installation

Through [Composer](http://getcomposer.org) as [chubbyphp/chubbyphp-deserialization][1].

```sh
composer require chubbyphp/chubbyphp-deserialization "^2.18"
```

## Usage

### Accessor

 * [MethodAccessor][2]
 * [PropertyAccessor][3]

### Container

 * [DecoderFactory][40]
 * [DenormalizerFactory][41]
 * [DenormalizerObjectMappingRegistryFactory][42]
 * [DeserializerFactory][43]

### Decoder

 * [Decoder][4]

#### Type Decoder

 * [JsonTypeDecoder][5]
 * [JsonxTypeDecoder][6]
 * [UrlEncodedTypeDecoder][7]
 * [XmlTypeDecoder][8]
 * [YamlTypeDecoder][9]

### Denormalizer

 * [Denormalizer][10]

#### Field Denormalizer

 * [CallbackFieldDenormalizer][11]
 * [DateTimeFieldDenormalizer][12]
 * [FieldDenormalizer][13]
 * [ConvertTypeFieldDenormalizer][14]

##### Relation Field Denormalizer

 * [EmbedManyFieldDenormalizer][15]
 * [EmbedOneFieldDenormalizer][16]
 * [ReferenceManyFieldDenormalizer][17]
 * [ReferenceOneFieldDenormalizer][18]

#### Denormalizer Context

 * [DenormalizerContext][19]
 * [DenormalizerContextBuilder][20]

### DenormalizerObjectMappingRegistry

* [DenormalizerObjectMappingRegistry][21]

### Deserializer

```php
<?php

use Chubbyphp\Deserialization\Decoder\Decoder;
use Chubbyphp\Deserialization\Decoder\JsonTypeDecoder;
use Chubbyphp\Deserialization\Decoder\JsonxTypeDecoder;
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
        new JsonxTypeDecoder(),
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
//    'application/x-jsonx',
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

 * [CallableDenormalizationObjectMapping][25]
 * [LazyDenormalizationObjectMapping][26]


### ServiceFactory

* [DeserializationServiceFactory][27]

### ServiceProvider

* [DeserializationServiceProvider][28]

## Copyright

Dominik Zogg 2020


[1]: https://packagist.org/packages/chubbyphp/chubbyphp-deserialization

[2]: doc/Accessor/MethodAccessor.md
[3]: doc/Accessor/PropertyAccessor.md

[4]: doc/Decoder/Decoder.md

[5]: doc/Decoder/JsonTypeDecoder.md
[6]: doc/Decoder/JsonxTypeDecoder.md
[7]: doc/Decoder/UrlEncodedTypeDecoder.md
[8]: doc/Decoder/XmlTypeDecoder.md
[9]: doc/Decoder/YamlTypeDecoder.md

[10]: doc/Denormalizer/Denormalizer.md

[11]: doc/Denormalizer/CallbackFieldDenormalizer.md
[12]: doc/Denormalizer/DateTimeFieldDenormalizer.md
[13]: doc/Denormalizer/FieldDenormalizer.md
[14]: doc/Denormalizer/ConvertTypeFieldDenormalizer.md

[15]: doc/Denormalizer/Relation/EmbedManyFieldDenormalizer.md
[16]: doc/Denormalizer/Relation/EmbedOneFieldDenormalizer.md
[17]: doc/Denormalizer/Relation/ReferenceManyFieldDenormalizer.md
[18]: doc/Denormalizer/Relation/ReferenceOneFieldDenormalizer.md

[19]: doc/Denormalizer/DenormalizerContext.md
[20]: doc/Denormalizer/DenormalizerContextBuilder.md

[21]: doc/Denormalizer/DenormalizerObjectMappingRegistry.md

[21]: doc/Mapping/DenormalizationFieldMapping.md
[22]: doc/Mapping/DenormalizationFieldMappingBuilder.md

[23]: doc/Mapping/AdvancedDenormalizationObjectMapping.md
[24]: doc/Mapping/SimpleDenormalizationObjectMapping.md

[25]: doc/Mapping/CallableDenormalizationObjectMapping.md
[26]: doc/Mapping/LazyDenormalizationObjectMapping.md

[27]: doc/ServiceFactory/DeserializationServiceFactory.md

[28]: doc/ServiceProvider/DeserializationServiceProvider.md

[40]: doc/Container/DecoderFactory.md
[41]: doc/Container/DenormalizerFactory.md
[42]: doc/Container/DenormalizerObjectMappingRegistryFactory.md
[43]: doc/Container/DeserializerFactory.md
