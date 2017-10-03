<?php

declare(strict_types=1);

namespace Chubbyphp\Deserialization\Denormalizer;

use Chubbyphp\Deserialization\DeserializerLogicException;
use Chubbyphp\Deserialization\DeserializerRuntimeException;
use Chubbyphp\Deserialization\Mapping\DenormalizingFieldMappingInterface;
use Chubbyphp\Deserialization\Mapping\DenormalizingObjectMappingInterface;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;

final class Denormalizer implements DenormalizerInterface
{
    /**
     * @var DenormalizingObjectMappingInterface[]
     */
    private $objectMappings;

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
        foreach ($objectMappings as $objectMapping) {
            $this->addObjectMapping($objectMapping);
        }

        $this->logger = $logger ?? new NullLogger();
    }

    /**
     * @param DenormalizingObjectMappingInterface $objectMapping
     */
    private function addObjectMapping(DenormalizingObjectMappingInterface $objectMapping)
    {
        $this->objectMappings[$objectMapping->getClass()] = $objectMapping;
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

        $type = $data['_type'] ?? null;

        unset($data['_type']);

        if (!is_object($object)) {
            $factory = $objectMapping->getFactory($type);
            $object = $factory();
        }

        foreach ($objectMapping->getDenormalizingFieldMappings() as $denormalizingFieldMapping) {
            $this->denormalizeField($context, $denormalizingFieldMapping, $path, $data, $object);

            unset($data[$denormalizingFieldMapping->getName()]);
        }

        if ([] !== $data && !$context->isAllowedAdditionalFields()) {
            $this->handleNotAllowedAddtionalFields($path, array_keys($data));
        }

        return $object;
    }

    /**
     * @param string $class
     *
     * @return DenormalizingObjectMappingInterface
     */
    private function getObjectMapping(string $class): DenormalizingObjectMappingInterface
    {
        if (isset($this->objectMappings[$class])) {
            return $this->objectMappings[$class];
        }

        $exception = DeserializerLogicException::createMissingMapping($class);

        $this->logger->error('deserialize: {exception}', ['exception' => $exception->getMessage()]);

        throw $exception;
    }

    /**
     * @param DenormalizerContextInterface       $context
     * @param DenormalizingFieldMappingInterface $denormalizingFieldMapping
     * @param string                             $path
     * @param array                              $data
     * @param $object
     */
    private function denormalizeField(
        DenormalizerContextInterface $context,
        DenormalizingFieldMappingInterface $denormalizingFieldMapping,
        string $path,
        array $data,
        $object
    ) {
        $name = $denormalizingFieldMapping->getName();
        if (!array_key_exists($name, $data)) {
            return;
        }

        $fieldDenormalizer = $denormalizingFieldMapping->getFieldDenormalizer();

        if (!$this->isWithinGroup($context, $denormalizingFieldMapping)) {
            return;
        }

        $subPath = $this->getSubPathByName($path, $name);

        $this->logger->info('deserialize: path {path}', ['path' => $subPath]);

        $fieldDenormalizer->denormalizeField($subPath, $object, $data[$name], $context, $this);
    }

    /**
     * @param string $path
     * @param $names
     */
    private function handleNotAllowedAddtionalFields(string $path, $names)
    {
        $exception = DeserializerRuntimeException::createNotAllowedAddtionalFields(
            $this->getSubPathsByNames($path, $names)
        );

        $this->logger->error('deserialize: {exception}', ['exception' => $exception->getMessage()]);

        throw $exception;
    }

    /**
     * @param DenormalizerContextInterface       $context
     * @param DenormalizingFieldMappingInterface $fieldMapping
     *
     * @return bool
     */
    private function isWithinGroup(
        DenormalizerContextInterface $context,
        DenormalizingFieldMappingInterface $fieldMapping
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
