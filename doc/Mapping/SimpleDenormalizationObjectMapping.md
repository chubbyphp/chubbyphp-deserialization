# SimpleDenormalizationObjectMapping

## Mapping

### ModelMapping

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
     */
    public function getDenormalizationFactory(
        string $path,
        string $type = null
    ): callable {
        return function () {
            return new Model();
        };
    }

    /**
     * @param string      $path
     * @param string|null $type
     *
     * @return array<int, DenormalizationFieldMappingInterface>
     */
    public function getDenormalizationFieldMappings(
        string $path,
        string $type = null
    ): array {
        return [
            DenormalizationFieldMappingBuilder::create('name')
                ->getMapping(),
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
