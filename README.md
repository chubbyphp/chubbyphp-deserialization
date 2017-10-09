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
composer require chubbyphp/chubbyphp-deserialization "~2.0@dev"
```

## Usage

### Accessor

 * [MethodAccessor][2]
 * [PropertyAccessor][3]

### Decoder

```php
<?php

use Chubbyphp\Deserialization\Decoder\Decoder;
use Chubbyphp\Deserialization\Decoder\JsonDecoderType;

$decoder = new Decoder([new JsonDecoderType]);

$data = $decoder->decode('{"name": "php"}', 'application/json');

print_r($data); // ['name' => 'php']
```

 * [JsonDecoderType][4]
 * [UrlEncodedDecoderType][5]
 * [XmlDecoderType][6]
 * [YamlDecoderType][7]

### Denormalizer

```php
<?php

use Chubbyphp\Deserialization\Denormalizer\Denormalizer;
use MyProject\Deserialization\ModelMapping;
use MyProject\Model\Model;

$logger = ...;

$denormalizer = new Denormalizer([new ModelMapping()], $logger);

$model = $denormalizer->denormalize(Model::class, ['name' => 'php']);

echo $model->getName(); // 'php'
```

### Deserializer

```php
<?php

use Chubbyphp\Deserialization\Decoder\Decoder;
use Chubbyphp\Deserialization\Decoder\JsonDecoderType;
use Chubbyphp\Deserialization\Denormalizer\Denormalizer;
use Chubbyphp\Deserialization\Deserializer;
use MyProject\Deserialization\ModelMapping;
use MyProject\Model\Model;

$logger = ...;

$deserializer = new Deserializer(
    new Decoder([new JsonDecoderType]),
    new Denormalizer([new ModelMapping()], $logger)
);

$model = $deserializer->deserialize(Model::class, '{"name": "php"}', 'application/json');

echo $model->getName(); // 'php'
```

### Mapping

```php
<?php

namespace MyProject\Deserialization;

use Chubbyphp\Deserialization\Mapping\DenormalizationFieldMappingBuilder;
use Chubbyphp\Deserialization\Mapping\DenormalizationFieldMappingInterface;
use Chubbyphp\Deserialization\Mapping\DenormalizationObjectMappingInterface;
use MyProject\Model\Model;

final class ModelMapping implements DenormalizationObjectMappingInterface
{
    /**
     * @param string $class
     *
     * @return bool
     */
    public function isDenormalizationResponsible(string $class): bool
    {
        return Model::class === $class;
    }

    /**
     * @param string|null $type
     *
     * @return callable
     */
    public function getDenormalizationFactory(string $type = null): callable
    {
        return function () {
            return new Model();
        };
    }

    /**
     * @param string|null $type
     *
     * @return DenormalizationFieldMappingInterface[]
     */
    public function getDenormalizationFieldMappings(string $type = null): array
    {
        return [
            DenormalizationFieldMappingBuilder::create('name')->getMapping(),
        ];
    }
}
```

## Copyright

Dominik Zogg 2017


[1]: https://packagist.org/packages/chubbyphp/chubbyphp-deserialization

[2]: doc/Accessor/MethodAccessor.md
[3]: doc/Accessor/PropertyAccessor.md

[4]: doc/Decoder/JsonDecoderType.md
[5]: doc/Decoder/UrlEncodedDecoderType.md
[6]: doc/Decoder/XmlDecoderType.md
[7]: doc/Decoder/YamlDecoderType.md