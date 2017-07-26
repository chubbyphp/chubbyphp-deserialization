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

## Installation

Through [Composer](http://getcomposer.org) as [chubbyphp/chubbyphp-deserialization][1].

```sh
composer require chubbyphp/chubbyphp-deserialization "~1.0"
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

 * [LazyObjectMapping][2]
 * [PropertyMapping][3]


### Registry

* [ObjectMappingRegistry][4]

### Provider

* [ValidationProvider][5]

## Copyright

Dominik Zogg 2017


[1]: https://packagist.org/packages/chubbyphp/chubbyphp-deserialization
[2]: doc/Mapping/LazyObjectMapping.md
[3]: doc/Mapping/PropertyMapping.md
[4]: doc/Registry/ObjectMappingRegistry.md
[5]: doc/Provider/DeserializationProvider.md
