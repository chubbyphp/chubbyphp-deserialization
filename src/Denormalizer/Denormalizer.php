<?php

declare(strict_types=1);

namespace Chubbyphp\Deserialization\Denormalizer;

use Chubbyphp\Deserialization\DeserializerLogicException;
use Chubbyphp\Deserialization\DeserializerRuntimeException;
use Chubbyphp\Deserialization\Mapping\DenormalizationFieldMappingInterface;
use Chubbyphp\Deserialization\Mapping\DenormalizationObjectMappingInterface;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;

final class Denormalizer implements DenormalizerInterface
{
    /**
     * @var array
     */
    private $objectMappings;

    /**
     * @var array
     */
    private $classToTypeMappings;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @param array                $objectMappings
     * @param LoggerInterface|null $logger
     */
    public function __construct(array $objectMappings, LoggerInterface $logger = null)
    {
        $this->objectMappings = [];
        $this->classToTypeMappings = [];
        foreach ($objectMappings as $objectMapping) {
            $this->addObjectMapping($objectMapping);
        }

        $this->logger = $logger ?? new NullLogger();
    }

    /**
     * @param DenormalizationObjectMappingInterface $objectMapping
     */
    private function addObjectMapping(DenormalizationObjectMappingInterface $objectMapping)
    {
        foreach ($objectMapping->getDenormalizationClassToTypeMappings() as $classToTypeMapping) {
            $this->objectMappings[$classToTypeMapping->getClass()] = [
                'mapping' => $objectMapping,
                'types' => $classToTypeMapping->getTypes(),
            ];
        }
    }

    /**
     * @param object|string                     $object
     * @param array                             $data
     * @param DenormalizerContextInterface|null $context
     * @param string                            $path
     *
     * @return object
     *
     * @throws DeserializerLogicException
     * @throws DeserializerRuntimeException
     */
    public function denormalize($object, array $data, DenormalizerContextInterface $context = null, string $path = '')
    {
        $context = $context ?? DenormalizerContextBuilder::create()->getContext();

        $class = is_object($object) ? get_class($object) : $object;
        $objectMapping = $this->getObjectMapping($class);

        $type = $this->getType($path, $class, $data['_type'] ?? null);

        unset($data['_type']);

        if (!is_object($object)) {
            $factory = $objectMapping->getDenormalizationFactory($type);
            $object = $factory();
        }

        foreach ($objectMapping->getDenormalizationFieldMappings($type) as $denormalizationFieldMapping) {
            $this->denormalizeField($context, $denormalizationFieldMapping, $path, $data, $object);

            unset($data[$denormalizationFieldMapping->getName()]);
        }

        if ([] !== $data && !$context->isAllowedAdditionalFields()) {
            $this->handleNotAllowedAddtionalFields($path, array_keys($data));
        }

        return $object;
    }

    /**
     * @param string $class
     *
     * @return DenormalizationObjectMappingInterface
     *
     * @throws DeserializerLogicException
     */
    private function getObjectMapping(string $class): DenormalizationObjectMappingInterface
    {
        if (isset($this->objectMappings[$class])) {
            return $this->objectMappings[$class]['mapping'];
        }

        $exception = DeserializerLogicException::createMissingMapping($class);

        $this->logger->error('deserialize: {exception}', ['exception' => $exception->getMessage()]);

        throw $exception;
    }

    /**
     * @param string      $path
     * @param string      $class
     * @param string|null $type
     *
     * @return string
     */
    private function getType(string $path, string $class, string $type = null): string
    {
        $allowedTypes = $this->objectMappings[$class]['types'];

        if (null !== $type) {
            if (in_array($type, $allowedTypes, true)) {
                return $type;
            }

            $exception = DeserializerRuntimeException::createInvalidObjectType($path, $type, $allowedTypes);

            $this->logger->notice('deserialize: {exception}', ['exception' => $exception->getMessage()]);

            throw $exception;
        }

        if (1 === count($allowedTypes)) {
            return reset($allowedTypes);
        }

        $exception = DeserializerRuntimeException::createMissingObjectType($path, $allowedTypes);

        $this->logger->notice('deserialize: {exception}', ['exception' => $exception->getMessage()]);

        throw $exception;
    }

    /**
     * @param DenormalizerContextInterface         $context
     * @param DenormalizationFieldMappingInterface $denormalizationFieldMapping
     * @param string                               $path
     * @param array                                $data
     * @param object                               $object
     */
    private function denormalizeField(
        DenormalizerContextInterface $context,
        DenormalizationFieldMappingInterface $denormalizationFieldMapping,
        string $path,
        array $data,
        $object
    ) {
        $name = $denormalizationFieldMapping->getName();
        if (!array_key_exists($name, $data)) {
            return;
        }

        $fieldDenormalizer = $denormalizationFieldMapping->getFieldDenormalizer();

        if (!$this->isWithinGroup($context, $denormalizationFieldMapping)) {
            return;
        }

        $subPath = $this->getSubPathByName($path, $name);

        $this->logger->info('deserialize: path {path}', ['path' => $subPath]);

        $fieldDenormalizer->denormalizeField($subPath, $object, $data[$name], $context, $this);
    }

    /**
     * @param string $path
     * @param array  $names
     */
    private function handleNotAllowedAddtionalFields(string $path, array $names)
    {
        $exception = DeserializerRuntimeException::createNotAllowedAddtionalFields(
            $this->getSubPathsByNames($path, $names)
        );

        $this->logger->notice('deserialize: {exception}', ['exception' => $exception->getMessage()]);

        throw $exception;
    }

    /**
     * @param DenormalizerContextInterface         $context
     * @param DenormalizationFieldMappingInterface $fieldMapping
     *
     * @return bool
     */
    private function isWithinGroup(
        DenormalizerContextInterface $context,
        DenormalizationFieldMappingInterface $fieldMapping
    ): bool {
        if ([] === $groups = $context->getGroups()) {
            return true;
        }

        foreach ($fieldMapping->getGroups() as $group) {
            if (in_array($group, $groups, true)) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param string $path
     * @param string $name
     *
     * @return string
     */
    private function getSubPathByName(string $path, string $name): string
    {
        return '' === $path ? $name : $path.'.'.$name;
    }

    /**
     * @param string $path
     * @param array  $names
     *
     * @return array
     */
    private function getSubPathsByNames(string $path, array $names): array
    {
        $subPaths = [];
        foreach ($names as $name) {
            $subPaths[] = $this->getSubPathByName($path, $name);
        }

        return $subPaths;
    }
}
