<?php

declare(strict_types=1);

namespace Chubbyphp\Deserialization\Denormalizer;

use Chubbyphp\Deserialization\Accessor\PropertyAccessor;
use Chubbyphp\Deserialization\DeserializerLogicException;
use Chubbyphp\Deserialization\DeserializerRuntimeException;
use Chubbyphp\Deserialization\Mapping\DenormalizationFieldMappingInterface;
use Chubbyphp\Deserialization\Mapping\DenormalizationObjectMappingInterface;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;

final class Denormalizer implements DenormalizerInterface
{
    /**
     * @var DenormalizerObjectMappingRegistryInterface
     */
    private $denormalizerObjectMappingRegistry;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @param DenormalizerObjectMappingRegistryInterface $denormalizerObjectMappingRegistry
     * @param LoggerInterface|null                       $logger
     */
    public function __construct(
        DenormalizerObjectMappingRegistryInterface $denormalizerObjectMappingRegistry,
        LoggerInterface $logger = null
    ) {
        $this->denormalizerObjectMappingRegistry = $denormalizerObjectMappingRegistry;
        $this->logger = $logger ?? new NullLogger();
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

        $type = null;
        if (isset($data['_type'])) {
            $type = $data['_type'];

            unset($data['_type']);
        }

        $isNew = false;
        if (!is_object($object)) {
            $isNew = true;
            $object = $this->createNewObject($objectMapping, $path, $type);
        }

        $missingFields = [];
        foreach ($objectMapping->getDenormalizationFieldMappings($path, $type) as $denormalizationFieldMapping) {
            $name = $denormalizationFieldMapping->getName();

            if (!array_key_exists($name, $data)) {
                $missingFields[] = $name;

                continue;
            }

            $this->denormalizeField($context, $denormalizationFieldMapping, $path, $name, $data, $object);

            unset($data[$name]);
        }

        $allowedAdditionalFields = $context->getAllowedAdditionalFields();

        if (null !== $allowedAdditionalFields
            && [] !== $fields = array_diff(array_keys($data), $allowedAdditionalFields)
        ) {
            $this->handleNotAllowedAdditionalFields($path, $fields);
        }

        if (!$isNew) {
            $this->resetMissingFields($context, $objectMapping, $object, $missingFields, $path, $type);
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
        try {
            return $this->denormalizerObjectMappingRegistry->getObjectMapping($class);
        } catch (DeserializerLogicException $exception) {
            $this->logger->error('deserialize: {exception}', ['exception' => $exception->getMessage()]);

            throw $exception;
        }
    }

    /**
     * @param DenormalizationObjectMappingInterface $objectMapping
     * @param string                                $path
     * @param string|null                           $type
     *
     * @return object
     */
    private function createNewObject(
        DenormalizationObjectMappingInterface $objectMapping,
        string $path,
        string $type = null
    ) {
        $factory = $objectMapping->getDenormalizationFactory($path, $type);
        $object = $factory();

        if (is_object($object)) {
            return $object;
        }

        $exception = DeserializerLogicException::createFactoryDoesNotReturnObject($path, gettype($object));

        $this->logger->error('deserialize: {exception}', ['exception' => $exception->getMessage()]);

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
        string $name,
        array $data,
        $object
    ) {
        if (!$this->isWithinGroup($context, $denormalizationFieldMapping)) {
            return;
        }

        $subPath = $this->getSubPathByName($path, $name);

        $this->logger->info('deserialize: path {path}', ['path' => $subPath]);

        $fieldDenormalizer = $denormalizationFieldMapping->getFieldDenormalizer();
        $fieldDenormalizer->denormalizeField($subPath, $object, $data[$name], $context, $this);
    }

    /**
     * @param string $path
     * @param array  $names
     */
    private function handleNotAllowedAdditionalFields(string $path, array $names)
    {
        $exception = DeserializerRuntimeException::createNotAllowedAdditionalFields(
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
     * @param string     $path
     * @param string|int $name
     *
     * @return string
     */
    private function getSubPathByName(string $path, $name): string
    {
        return '' === $path ? (string) $name : $path.'.'.$name;
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

    /**
     * @param DenormalizerContextInterface          $context
     * @param DenormalizationObjectMappingInterface $objectMapping
     * @param object                                $object
     * @param array                                 $missingFields
     * @param string                                $path
     * @param string|null                           $type
     */
    private function resetMissingFields(
        DenormalizerContextInterface $context,
        DenormalizationObjectMappingInterface $objectMapping,
        $object,
        array $missingFields,
        string $path,
        string $type = null
    ) {
        if (!method_exists($context, 'isResetMissingFields') || !$context->isResetMissingFields()) {
            return;
        }

        $factory = $objectMapping->getDenormalizationFactory($path, $type);

        $newObject = $factory();

        foreach ($missingFields as $missingField) {
            $accessor = new PropertyAccessor($missingField);
            $accessor->setValue($object, $accessor->getValue($newObject));
        }
    }
}
