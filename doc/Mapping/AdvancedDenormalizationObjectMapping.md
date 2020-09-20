# AdvancedDenormalizationObjectMapping

## Mapping

### ModelMapping

```php
<?php

namespace MyProject\Mapping;

use Chubbyphp\Deserialization\Accessor\PropertyAccessor;
use Chubbyphp\Deserialization\Denormalizer\Relation\EmbedManyFieldDenormalizer;
use Chubbyphp\Deserialization\Denormalizer\Relation\EmbedOneFieldDenormalizer;
use Chubbyphp\Deserialization\DeserializerRuntimeException;
use Chubbyphp\Deserialization\Mapping\DenormalizationFieldMappingBuilder;
use Chubbyphp\Deserialization\Mapping\DenormalizationFieldMappingInterface;
use Chubbyphp\Deserialization\Mapping\DenormalizationObjectMappingInterface;
use Chubbyphp\Deserialization\Policy\GroupPolicy;
use MyProject\Model\AbstractManyModel;
use MyProject\Model\Model;

final class ModelMapping implements DenormalizationObjectMappingInterface
{
    /**
     * @return string
     */
    public function getClass(): string
    {
        return Model::class;
    }

    /**
     * @param string      $path
     * @param string|null $type
     *
     * @return callable
     *
     * @throws DeserializerRuntimeException
     */
    public function getDenormalizationFactory(string $path, string $type = null): callable
    {
        return function () {
            return new Model();
        };
    }

    /**
     * @param string      $path
     * @param string|null $type
     *
     * @return array<int, DenormalizationFieldMappingInterface>
     *
     * @throws DeserializerRuntimeException
     */
    public function getDenormalizationFieldMappings(string $path, string $type = null): array
    {
        return [
            DenormalizationFieldMappingBuilder::create('name')
                ->setPolicy(new GroupPolicy(['baseInformation']))
                ->getMapping(),
            DenormalizationFieldMappingBuilder::createEmbedOne('one', OneModel::class)->getMapping(),
            DenormalizationFieldMappingBuilder::createEmbedMany('manies', AbstractManyModel::class)->getMapping(),
        ];
    }
}
```

### BaseManyModelMapping

```php
<?php

namespace MyProject\Mapping;

use Chubbyphp\Deserialization\DeserializerRuntimeException;
use Chubbyphp\Deserialization\Mapping\DenormalizationFieldMappingInterface;
use Chubbyphp\Deserialization\Mapping\DenormalizationObjectMappingInterface;
use MyProject\Model\AbstractManyModel;

final class BaseManyModelMapping implements DenormalizationObjectMappingInterface
{
    /**
     * @var ManyModelMapping
     */
    private $modelMapping;

    /**
     * @var array
     */
    private $supportedTypes;

    /**
     * @param ManyModelMapping $modelMapping
     * @param array            $supportedTypes
     */
    public function __construct(
        ManyModelMapping $modelMapping,
        array $supportedTypes
    ) {
        $this->modelMapping = $modelMapping;
        $this->supportedTypes = $supportedTypes;
    }

    /**
     * @return string
     */
    public function getClass(): string
    {
        return AbstractManyModel::class;
    }

    /**
     * @param string      $path
     * @param string|null $type
     *
     * @return callable
     *
     * @throws DeserializerRuntimeException
     */
    public function getDenormalizationFactory(
        string $path,
        string $type = null
    ): callable {
        if (null === $type) {
            throw DeserializerRuntimeException::createMissingObjectType(
                $path,
                $this->supportedTypes
            );
        }

        if ('many-model' === $type) {
            return $this->modelMapping
                ->getDenormalizationFactory($path);
        }

        throw DeserializerRuntimeException::createInvalidObjectType(
            $path,
            $type,
            $this->supportedTypes
        );
    }

    /**
     * @param string      $path
     * @param string|null $type
     *
     * @return array<int, DenormalizationFieldMappingInterface>
     *
     * @throws DeserializerRuntimeException
     */
    public function getDenormalizationFieldMappings(
        string $path,
        string $type = null
    ): array {
        if (null === $type) {
            throw DeserializerRuntimeException::createMissingObjectType(
                $path,
                $this->supportedTypes
            );
        }

        if ('many-model' === $type) {
            return $this->modelMapping
                ->getDenormalizationFieldMappings($path);
        }

        throw DeserializerRuntimeException::createInvalidObjectType(
            $path,
            $type,
            $this->supportedTypes
        );
    }
}
```

### ManyModelMapping

```php
<?php

namespace MyProject\Mapping;

use Chubbyphp\Deserialization\DeserializerRuntimeException;
use Chubbyphp\Deserialization\Mapping\DenormalizationFieldMappingBuilder;
use Chubbyphp\Deserialization\Mapping\DenormalizationFieldMappingInterface;
use Chubbyphp\Deserialization\Mapping\DenormalizationObjectMappingInterface;
use MyProject\Model\ManyModel;

final class ManyModelMapping implements DenormalizationObjectMappingInterface
{
    /**
     * @return string
     */
    public function getClass(): string
    {
        return ManyModel::class;
    }

    /**
     * @param string      $path
     * @param string|null $type
     *
     * @return callable
     *
     * @throws DeserializerRuntimeException
     */
    public function getDenormalizationFactory(
        string $path,
        string $type = null
    ): callable {
        return function () {
            return new ManyModel();
        };
    }

    /**
     * @param string      $path
     * @param string|null $type
     *
     * @return array<int, DenormalizationFieldMappingInterface>
     *
     * @throws DeserializerRuntimeException
     */
    public function getDenormalizationFieldMappings(
        string $path, string $type = null
    ): array {
        return [
            DenormalizationFieldMappingBuilder::create('name')->getMapping(),
            DenormalizationFieldMappingBuilder::create('value')->getMapping(),
        ];
    }
}
```

### OneModelMapping

```php
<?php

namespace MyProject\Mapping;

use Chubbyphp\Deserialization\DeserializerRuntimeException;
use Chubbyphp\Deserialization\Mapping\DenormalizationFieldMappingBuilder;
use Chubbyphp\Deserialization\Mapping\DenormalizationFieldMappingInterface;
use Chubbyphp\Deserialization\Mapping\DenormalizationObjectMappingInterface;
use MyProject\Model\OneModel;

final class OneModelMapping implements DenormalizationObjectMappingInterface
{
    /**
     * @return string
     */
    public function getClass(): string
    {
        return OneModel::class;
    }

    /**
     * @param string      $path
     * @param string|null $type
     *
     * @return callable
     *
     * @throws DeserializerRuntimeException
     */
    public function getDenormalizationFactory(
        string $path,
        string $type = null
    ): callable {
        return function () {
            return new OneModel();
        };
    }

    /**
     * @param string      $path
     * @param string|null $type
     *
     * @return array<int, DenormalizationFieldMappingInterface>
     *
     * @throws DeserializerRuntimeException
     */
    public function getDenormalizationFieldMappings(
        string $path, string $type = null
    ): array {
        return [
            DenormalizationFieldMappingBuilder::create('name')->getMapping(),
            DenormalizationFieldMappingBuilder::create('value')->getMapping(),
        ];
    }
}
```

## Model

### Model

```php
<?php

namespace MyProject\Model;

final class Model
{
    /**
     * @var string
     */
    private $name;

    /**
     * @var OneModel|null
     */
    private $one;

    /**
     * @var AbstractManyModel[]
     */
    private $manies;

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     *
     * @return self
     */
    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return OneModel|null
     */
    public function getOne()
    {
        return $this->one;
    }

    /**
     * @param OneModel|null $one
     * @return self
     */
    public function setOne(OneModel $one = null)
    {
        $this->one = $one;

        return $this;
    }

    /**
     * @return AbstractManyModel[]
     */
    public function getManies(): array
    {
        return $this->manies;
    }

    /**
     * @param AbstractManyModel[] $manies
     * @return self
     */
    public function setManies(array $manies): self
    {
        $this->manies = $manies;

        return $this;
    }
}
```

### AbstractManyModel

```php
<?php

namespace MyProject\Model;

abstract class AbstractManyModel
{
    /**
     * @var string
     */
    protected $name;

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     *
     * @return self
     */
    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }
}
```

### ManyModel

```php
<?php

namespace MyProject\Model;

final class ManyModel extends AbstractManyModel
{
    /**
     * @var string
     */
    private $value;

    /**
     * @return string
     */
    public function getValue(): string
    {
        return $this->value;
    }

    /**
     * @param string $value
     *
     * @return self
     */
    public function setValue(string $value): self
    {
        $this->value = $value;

        return $this;
    }
}
```

### ManyModel

```php
<?php

namespace MyProject\Model;

final class OneModel
{
    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $value;

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     * @return self
     */
    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return string
     */
    public function getValue(): string
    {
        return $this->value;
    }

    /**
     * @param string $value
     * @return self
     */
    public function setValue(string $value): self
    {
        $this->value = $value;

        return $this;
    }
}
```

