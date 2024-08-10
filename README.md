# chubbyphp-deserialization

[![CI](https://github.com/chubbyphp/chubbyphp-deserialization/actions/workflows/ci.yml/badge.svg)](https://github.com/chubbyphp/chubbyphp-deserialization/actions/workflows/ci.yml)
[![Coverage Status](https://coveralls.io/repos/github/chubbyphp/chubbyphp-deserialization/badge.svg?branch=master)](https://coveralls.io/github/chubbyphp/chubbyphp-deserialization?branch=master)
[![Mutation testing badge](https://img.shields.io/endpoint?style=flat&url=https%3A%2F%2Fbadge-api.stryker-mutator.io%2Fgithub.com%2Fchubbyphp%2Fchubbyphp-deserialization%2Fmaster)](https://dashboard.stryker-mutator.io/reports/github.com/chubbyphp/chubbyphp-deserialization/master)
[![Latest Stable Version](https://poser.pugx.org/chubbyphp/chubbyphp-deserialization/v)](https://packagist.org/packages/chubbyphp/chubbyphp-deserialization)
[![Total Downloads](https://poser.pugx.org/chubbyphp/chubbyphp-deserialization/downloads)](https://packagist.org/packages/chubbyphp/chubbyphp-deserialization)
[![Monthly Downloads](https://poser.pugx.org/chubbyphp/chubbyphp-deserialization/d/monthly)](https://packagist.org/packages/chubbyphp/chubbyphp-deserialization)

[![bugs](https://sonarcloud.io/api/project_badges/measure?project=chubbyphp_chubbyphp-deserialization&metric=bugs)](https://sonarcloud.io/dashboard?id=chubbyphp_chubbyphp-deserialization)
[![code_smells](https://sonarcloud.io/api/project_badges/measure?project=chubbyphp_chubbyphp-deserialization&metric=code_smells)](https://sonarcloud.io/dashboard?id=chubbyphp_chubbyphp-deserialization)
[![coverage](https://sonarcloud.io/api/project_badges/measure?project=chubbyphp_chubbyphp-deserialization&metric=coverage)](https://sonarcloud.io/dashboard?id=chubbyphp_chubbyphp-deserialization)
[![duplicated_lines_density](https://sonarcloud.io/api/project_badges/measure?project=chubbyphp_chubbyphp-deserialization&metric=duplicated_lines_density)](https://sonarcloud.io/dashboard?id=chubbyphp_chubbyphp-deserialization)
[![ncloc](https://sonarcloud.io/api/project_badges/measure?project=chubbyphp_chubbyphp-deserialization&metric=ncloc)](https://sonarcloud.io/dashboard?id=chubbyphp_chubbyphp-deserialization)
[![sqale_rating](https://sonarcloud.io/api/project_badges/measure?project=chubbyphp_chubbyphp-deserialization&metric=sqale_rating)](https://sonarcloud.io/dashboard?id=chubbyphp_chubbyphp-deserialization)
[![alert_status](https://sonarcloud.io/api/project_badges/measure?project=chubbyphp_chubbyphp-deserialization&metric=alert_status)](https://sonarcloud.io/dashboard?id=chubbyphp_chubbyphp-deserialization)
[![reliability_rating](https://sonarcloud.io/api/project_badges/measure?project=chubbyphp_chubbyphp-deserialization&metric=reliability_rating)](https://sonarcloud.io/dashboard?id=chubbyphp_chubbyphp-deserialization)
[![security_rating](https://sonarcloud.io/api/project_badges/measure?project=chubbyphp_chubbyphp-deserialization&metric=security_rating)](https://sonarcloud.io/dashboard?id=chubbyphp_chubbyphp-deserialization)
[![sqale_index](https://sonarcloud.io/api/project_badges/measure?project=chubbyphp_chubbyphp-deserialization&metric=sqale_index)](https://sonarcloud.io/dashboard?id=chubbyphp_chubbyphp-deserialization)
[![vulnerabilities](https://sonarcloud.io/api/project_badges/measure?project=chubbyphp_chubbyphp-deserialization&metric=vulnerabilities)](https://sonarcloud.io/dashboard?id=chubbyphp_chubbyphp-deserialization)

## Description

A simple deserialization.

## Requirements

 * php: ^8.1
 * chubbyphp/chubbyphp-decode-encode: ^1.1
 * psr/http-message: ^1.1|^2.0
 * psr/log: ^2.0|^3.0

## Suggest

 * chubbyphp/chubbyphp-container: ^2.2
 * pimple/pimple: ^3.5
 * psr/container: ^2.0.2
 * symfony/config: ^5.4.31|^6.3.8|^7.0 (symfony integration)
 * symfony/dependency-injection: ^5.4.31|^6.3.8|^7.0 (symfony integration)

## Installation

Through [Composer](http://getcomposer.org) as [chubbyphp/chubbyphp-deserialization][1].

```sh
composer require chubbyphp/chubbyphp-deserialization "^4.1"
```

## Usage

### Accessor

 * [MethodAccessor][2]
 * [PropertyAccessor][3]

### Denormalizer

 * [Denormalizer][10]

#### Field Denormalizer

 * [CallbackFieldDenormalizer][11]
 * [DateTimeImmutableFieldDenormalizer][12]
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

use Chubbyphp\DecodeEncode\Decoder\Decoder;
use Chubbyphp\DecodeEncode\Decoder\JsonTypeDecoder;
use Chubbyphp\DecodeEncode\Decoder\JsonxTypeDecoder;
use Chubbyphp\DecodeEncode\Decoder\UrlEncodedTypeDecoder;
use Chubbyphp\DecodeEncode\Decoder\XmlTypeDecoder;
use Chubbyphp\DecodeEncode\Decoder\YamlTypeDecoder;
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
//    'application/jsonx+xml',
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
 * [DenormalizationFieldMappingFactory][22]

#### DenormalizationObjectMapping

 * [AdvancedDenormalizationObjectMapping][23]
 * [SimpleDenormalizationObjectMapping][24]

#### LazyDenormalizationObjectMapping

 * [CallableDenormalizationObjectMapping][25]
 * [LazyDenormalizationObjectMapping][26]

### Policy

* [AndPolicy][27]
* [CallbackPolicy][28]
* [GroupPolicy][29]
* [NotPolicy][30]
* [NullPolicy][31]
* [OrPolicy][32]

### ServiceFactory

#### chubbyphp-container

 * [DeserializationServiceFactory][33]

#### chubbyphp-laminas-config-factory

 * [DenormalizationFieldMappingFactoryFactory][41]
 * [DenormalizerFactory][42]
 * [DenormalizerObjectMappingRegistryFactory][43]
 * [DeserializerFactory][44]

### ServiceProvider

* [DeserializationServiceProvider][34]

## Copyright

2024 Dominik Zogg


[1]: https://packagist.org/packages/chubbyphp/chubbyphp-deserialization

[2]: doc/Accessor/MethodAccessor.md
[3]: doc/Accessor/PropertyAccessor.md

[10]: doc/Denormalizer/Denormalizer.md

[11]: doc/Denormalizer/CallbackFieldDenormalizer.md
[12]: doc/Denormalizer/DateTimeImmutableFieldDenormalizer.md
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
[22]: doc/Mapping/DenormalizationFieldMappingFactory.md

[23]: doc/Mapping/AdvancedDenormalizationObjectMapping.md
[24]: doc/Mapping/SimpleDenormalizationObjectMapping.md

[25]: doc/Mapping/CallableDenormalizationObjectMapping.md
[26]: doc/Mapping/LazyDenormalizationObjectMapping.md

[27]: doc/Policy/AndPolicy.md
[28]: doc/Policy/CallbackPolicy.md
[29]: doc/Policy/GroupPolicy.md
[30]: doc/Policy/NotPolicy.md
[31]: doc/Policy/NullPolicy.md
[32]: doc/Policy/OrPolicy.md

[33]: doc/ServiceFactory/DeserializationServiceFactory.md

[34]: doc/ServiceProvider/DeserializationServiceProvider.md

[41]: doc/ServiceFactory/DenormalizationFieldMappingFactoryFactory.md
[42]: doc/ServiceFactory/DenormalizerFactory.md
[43]: doc/ServiceFactory/DenormalizerObjectMappingRegistryFactory.md
[44]: doc/ServiceFactory/DeserializerFactory.md
