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

        if (!is_object($object)) {
            $object = $this->createNewObject($objectMapping, $path, $type);
        }

        foreach ($objectMapping->getDenormalizationFieldMappings($path, $type) as $fieldMapping) {
            $this->denormalizeField($context, $fieldMapping, $path, $data, $object);

            unset($data[$fieldMapping->getName()]);
        }

        if (null !== $context->getAllowedAdditionalFields()
            && [] !== $fields = array_diff(array_keys($data), $context->getAllowedAdditionalFields())
        ) {
            $this->handleNotAllowedAdditionalFields($path, $fields);
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
     * @param DenormalizationFieldMappingInterface $fieldMapping
     * @param string                               $path
     * @param array                                $data
     * @param object                               $object
     */
    private function denormalizeField(
        DenormalizerContextInterface $context,
        DenormalizationFieldMappingInterface $fieldMapping,
        string $path,
        array $data,
        $object
    ) {
        $name = $fieldMapping->getName();
        if (!array_key_exists($name, $data)) {
            return;
        }

        $fieldDenormalizer = $fieldMapping->getFieldDenormalizer();

        if (!$this->isWithinGroup($context, $fieldMapping)) {
            return;
        }

        $subPath = $this->getSubPathByName($path, $name);

        $this->logger->info('deserialize: path {path}', ['path' => $subPath]);

        $fieldDenormalizer->denormalizeField(
            $subPath,
            $object,
            $this->forceType($fieldMapping, $data[$name]),
            $context,
            $this
        );
    }

    /**
     * @param DenormalizationFieldMappingInterface $fieldMapping
     * @param mixed                                $value
     *
     * @return mixed
     */
    private function forceType(DenormalizationFieldMappingInterface $fieldMapping, $value)
    {
        if (!method_exists($fieldMapping, 'getForceType')) {
            return $value;
        }

        if (null === $forceType = $fieldMapping->getForceType()) {
            return $value;
        }

        $type = gettype($value);

        if (!is_scalar($value) || $forceType === $type) {
            return $value;
        }

        if (!in_array($forceType, DenormalizationFieldMappingInterface::FORCETYPES, true)) {
            return $value;
        }

        $forcedValue = $value;
        settype($forcedValue, $forceType);

        if ((string) $value !== (string) $forcedValue) {
            return $value;
        }

        return $forcedValue;
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
}
