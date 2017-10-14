# AdvancedDenormalizationObjectMapping

## Mapping

### BaseChildModelMapping

```php
<?php

namespace MyProject\Mapping;

use Chubbyphp\Deserialization\DeserializerRuntimeException;
use Chubbyphp\Deserialization\Mapping\DenormalizationFieldMappingInterface;
use Chubbyphp\Deserialization\Mapping\DenormalizationObjectMappingInterface;
use MyProject\Model\AbstractChildModel;

final class BaseChildModelMapping implements DenormalizationObjectMappingInterface
{
    /**
     * @var ChildModelMapping
     */
    private $modelMapping;

    /**
     * @var array
     */
    private $supportedTypes;

    /**
     * @param ChildModelMapping $modelMapping
     * @param array             $supportedTypes
     */
    public function __construct(
        ChildModelMapping $modelMapping,
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
        return AbstractChildModel::class;
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

        if ('child-model' === $type) {
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
     * @return DenormalizationFieldMappingInterface[]
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

        if ('child-model' === $type) {
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

### ChildModelMapping

```php
<?php

namespace MyProject\Mapping;

use Chubbyphp\Deserialization\DeserializerRuntimeException;
use Chubbyphp\Deserialization\Mapping\DenormalizationFieldMappingBuilder;
use Chubbyphp\Deserialization\Mapping\DenormalizationFieldMappingInterface;
use Chubbyphp\Deserialization\Mapping\DenormalizationObjectMappingInterface;
use MyProject\Model\ChildModel;

final class ChildModelMapping implements DenormalizationObjectMappingInterface
{
    /**
     * @return string
     */
    public function getClass(): string
    {
        return ChildModel::class;
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
            return new ChildModel();
        };
    }

    /**
     * @param string      $path
     * @param string|null $type
     *
     * @return DenormalizationFieldMappingInterface[]
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

### ParentModelMapping

```php
<?php

namespace MyProject\Mapping;

use Chubbyphp\Deserialization\Accessor\PropertyAccessor;
use Chubbyphp\Deserialization\Denormalizer\CollectionFieldDenormalizer;
use Chubbyphp\Deserialization\DeserializerRuntimeException;
use Chubbyphp\Deserialization\Mapping\DenormalizationFieldMappingBuilder;
use Chubbyphp\Deserialization\Mapping\DenormalizationFieldMappingInterface;
use Chubbyphp\Deserialization\Mapping\DenormalizationObjectMappingInterface;
use MyProject\Model\AbstractChildModel;
use MyProject\Model\ParentModel;

final class ParentModelMapping implements DenormalizationObjectMappingInterface
{
    /**
     * @return string
     */
    public function getClass(): string
    {
        return ParentModel::class;
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
            return new ParentModel();
        };
    }

    /**
     * @param string      $path
     * @param string|null $type
     *
     * @return DenormalizationFieldMappingInterface[]
     *
     * @throws DeserializerRuntimeException
     */
    public function getDenormalizationFieldMappings(
        string $path,
        string $type = null
    ): array {
        return [
            DenormalizationFieldMappingBuilder::create('name')
                ->getMapping(),
            DenormalizationFieldMappingBuilder::create('children')
                ->setFieldDenormalizer(
                    new CollectionFieldDenormalizer(
                        AbstractChildModel::class,
                        new PropertyAccessor('children')
                    )
                )
                ->getMapping(),
        ];
    }
}
```

## Model

### AbstractChildModel

```php
<?php

namespace MyProject\Model;

abstract class AbstractChildModel
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

### ChildModel

```php
<?php

namespace MyProject\Model;

final class ChildModel extends AbstractChildModel
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

### ParentModel

```php
<?php

namespace MyProject\Model;

final class ParentModel
{
    /**
     * @var string
     */
    private $name;

    /**
     * @var ChildModel[]
     */
    private $children;

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
     * @return ChildModel[]
     */
    public function getChildren(): array
    {
        return $this->children;
    }

    /**
     * @param ChildModel[] $children
     *
     * @return self
     */
    public function setChildren(array $children): self
    {
        $this->children = $children;

        return $this;
    }
}
```
