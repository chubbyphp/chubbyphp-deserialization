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

 * chubbyphp/chubbyphp-model: ~3.0
 * container-interop/container-interop: ~1.0
 * doctrine/common: ~2.2
 * pimple/pimple: ~3.0
 * symfony/yaml: ~2.7|~3.0

## Installation

Through [Composer](http://getcomposer.org) as [chubbyphp/chubbyphp-deserialization][1].

```sh
composer require chubbyphp/chubbyphp-deserialization "~1.2"
```

## Usage

### Deserializer

```php
<?php

use Chubbyphp\Deserialization\Registry\ObjectMappingRegistry;
use Chubbyphp\Deserialization\Deserializer;
use MyProject\Model\Model;

use MyProject\Deserialization\ModelMapping;

$deserialize = new Deserializer(new ObjectMappingRegistry([new ModelMapping()]));

$model = $deserializer->deserializeByClass(['name' => 'name1'], Model::class);
$model->getName(); // name1

$model = $deserializer->deserializeByObject(['name' => 'name1'], new Model);
$model->getName(); // name1
```

#### Doctrine

 * [PropertyModelCollectionDeserializer][2]
 * [PropertyModelReferenceDeserializer][3]

#### Model

 * [PropertyModelCollectionDeserializer][4]
 * [PropertyModelReferenceDeserializer][5]

### Mapping

```php
<?php

namespace MyProject\Deserialization;

use Chubbyphp\Deserialization\Mapping\ObjectMappingInterface;
use Chubbyphp\Deserialization\Mapping\PropertyMapping;
use Chubbyphp\Deserialization\Mapping\PropertyMappingInterface;
use MyProject\Model\Model;

class ModelMapping implements ObjectMappingInterface
{
    /**
     * @return string
     */
    public function getClass(): string
    {
        return Model::class;
    }

    /**
     * @return callable
     */
    public function getFactory(): callable
    {
        return [Model::class, '__construct'];
    }

    /**
     * @return PropertyMappingInterface[]
     */
    public function getPropertyMappings(): array
    {
        return [
            new PropertyMapping('name'),
        ];
    }
}
```

 * [LazyObjectMapping][6]
 * [PropertyMapping][7]

### Provider

* [ValidationProvider][8]

### Registry

* [ObjectMappingRegistry][9]


### Transformer

```php
<?php

use Chubbyphp\Deserialization\Transformer;
use Chubbyphp\Deserialization\Transformer\JsonTransformer;
use Chubbyphp\Deserialization\Transformer\UrlEncodedTransformer;
use Chubbyphp\Deserialization\Transformer\XmlTransformer;
use Chubbyphp\Deserialization\Transformer\YamlTransformer;

$transformer = new Transformer([
    new JsonTransformer(),
    new UrlEncodedTransformer(),
    new XmlTransformer(),
    new YamlTransformer(),
]);

$contentTypes = $transformer->getContentTypes();

$data = $transformer->transform('{"key":"value"}', 'application/json');
```

* [JsonTransformer][10]
* [UrlEncodedTransformer][11]
* [XmlTransformer][12]
* [YamlTransformer][13]

## Copyright

Dominik Zogg 2017


[1]: https://packagist.org/packages/chubbyphp/chubbyphp-deserialization

[2]: doc/Doctrine/Deserializer/PropertyModelCollectionDeserializer.md
[3]: doc/Doctrine/Deserializer/PropertyModelReferenceDeserializer.md

[4]: doc/Model/Deserializer/PropertyModelCollectionDeserializer.md
[5]: doc/Model/Deserializer/PropertyModelReferenceDeserializer.md

[6]: doc/Mapping/LazyObjectMapping.md
[7]: doc/Mapping/PropertyMapping.md

[8]: doc/Provider/DeserializationProvider.md

[9]: doc/Registry/ObjectMappingRegistry.md

[10]: doc/Transformer/JsonTransformer.md
[11]: doc/Transformer/UrlEncodedTransformer.md
[12]: doc/Transformer/XmlTransformer.md
[13]: doc/Transformer/YamlTransformer.md


