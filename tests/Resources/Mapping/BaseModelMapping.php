<?php

declare(strict_types=1);

namespace Chubbyphp\Tests\Deserialization\Resources\Mapping;

use Chubbyphp\Deserialization\DeserializerRuntimeException;
use Chubbyphp\Deserialization\Mapping\DenormalizationFieldMappingInterface;
use Chubbyphp\Deserialization\Mapping\DenormalizationObjectMappingInterface;
use Chubbyphp\Tests\Deserialization\Resources\Model\AbstractModel;

final class BaseModelMapping implements DenormalizationObjectMappingInterface
{
    /**
     * @var ModelMapping
     */
    private $modelMapping;

    /**
     * @var array
     */
    private $supportedTypes;

    /**
     * @param ModelMapping $modelMapping
     * @param array        $supportedTypes
     */
    public function __construct(ModelMapping $modelMapping, array $supportedTypes)
    {
        $this->modelMapping = $modelMapping;
        $this->supportedTypes = $supportedTypes;
    }

    /**
     * @return string
     */
    public function getClass(): string
    {
        return AbstractModel::class;
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
        if (null === $type) {
            throw DeserializerRuntimeException::createMissingObjectType($path, $this->supportedTypes);
        }

        if ($type === 'model') {
            return $this->modelMapping->getDenormalizationFactory($path);
        }

        throw DeserializerRuntimeException::createInvalidObjectType($path, $type, $this->supportedTypes);
    }

    /**
     * @param string      $path
     * @param string|null $type
     *
     * @return DenormalizationFieldMappingInterface[]
     *
     * @throws DeserializerRuntimeException
     */
    public function getDenormalizationFieldMappings(string $path, string $type = null): array
    {
        if (null === $type) {
            throw DeserializerRuntimeException::createMissingObjectType($path, $this->supportedTypes);
        }

        if ($type === 'model') {
            return $this->modelMapping->getDenormalizationFieldMappings($path);
        }

        throw DeserializerRuntimeException::createInvalidObjectType($path, $type, $this->supportedTypes);
    }
}
