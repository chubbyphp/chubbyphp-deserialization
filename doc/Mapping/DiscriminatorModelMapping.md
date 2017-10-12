# DiscriminatorModelMapping

```php
<?php

namespace MyProject\Deserialization;

use Chubbyphp\Deserialization\DeserializerRuntimeException;
use Chubbyphp\Deserialization\Mapping\DenormalizationFieldMappingBuilder;
use Chubbyphp\Deserialization\Mapping\DenormalizationFieldMappingInterface;
use Chubbyphp\Deserialization\Mapping\DenormalizationObjectMappingInterface;
use MyProject\Model\AbstractModel;
use MyProject\Model\Model;

final class AbstractModelMapping implements DenormalizationObjectMappingInterface
{
    /**
     * @var ModelMapping
     */
    private $modelMapping;

    /**
     * @param ModelMapping $modelMapping
     */
    public function __construct(ModelMapping $modelMapping)
    {
        $this->modelMapping = $modelMapping;
    }

    /**
     * @return string
     */
    public function getClass(): string
    {
        return AbstractModel::class;
    }

    /**
     * @param string $type
     *
     * @return callable
     *
     * @throws DeserializerRuntimeException
     */
    public function getDenormalizationFactory(string $type): callable
    {
        if (null === $type) {
            throw DeserializerRuntimeException::createMissingObjectType(['model']);
        }

        switch ($type) {
            case 'model':
                return $this->modelMapping->getDenormalizationFactory($type, ['model']);
        }

        throw DeserializerRuntimeException::createInvalidObjectType();
    }

    /**
     * @param string $type
     *
     * @return DenormalizationFieldMappingInterface[]
     *
     * @throws DeserializerRuntimeException
     */
    public function getDenormalizationFieldMappings(string $type): array
    {
        if (null === $type) {
            throw DeserializerRuntimeException::createMissingObjectType(['model']);
        }

        switch ($type) {
            case 'model':
                return $this->modelMapping->getDenormalizationFieldMappings();
        }

        throw DeserializerRuntimeException::createInvalidObjectType($type, ['model']);
    }
}

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
     * @param string $type
     *
     * @return callable
     */
    public function getDenormalizationFactory(string $type): callable
    {
        return function () {
            return new Model();
        };
    }

    /**
     * @param string $type
     *
     * @return DenormalizationFieldMappingInterface[]
     */
    public function getDenormalizationFieldMappings(string $type): array
    {
        return [
            DenormalizationFieldMappingBuilder::create('name')->getMapping(),
        ];
    }
}
```