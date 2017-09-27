<?php

declare(strict_types=1);

namespace Chubbyphp\Deserialization\Denormalizer;

use Chubbyphp\Deserialization\Mapping\DenormalizingFieldMappingInterface;
use Chubbyphp\Deserialization\Mapping\DenormalizingObjectMappingInterface;

final class Denormalizer implements DenormalizerInterface
{
    /**
     * @var DenormalizingObjectMappingInterface[]
     */
    private $objectMappings;

    /**
     * @param DenormalizingObjectMappingInterface[] $objectMappings
     */
    public function __construct(array $objectMappings)
    {
        $this->objectMappings = [];
        foreach ($objectMappings as $objectMapping) {
            $this->addObjectMapping($objectMapping);
        }
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
     * @throws DenormalizerException
     */
    public function denormalize($object, array $data, DenormalizerContextInterface $context = null, string $path = '')
    {
        $context = $context ?? new DenormalizerContext();

        $class = is_object($object) ? get_class($object) : $object;
        $objectMapping = $this->getObjectMapping($class);

        if (!is_object($object)) {
            $factory = $objectMapping->getFactory();
            $object = $factory();
        }

        foreach ($objectMapping->getDenormalizingFieldMappings() as $denormalizingFieldMapping) {
            $name = $denormalizingFieldMapping->getName();
            if (!isset($data[$name]) && !$context->isReplaceMode()) {
                continue;
            }

            $fieldDenormalizer = $denormalizingFieldMapping->getFieldDenormalizer();

            $value = $data[$name] ?? $fieldDenormalizer->getDefault();

            unset($data[$name]);

            if (!$this->isWithinGroup($context, $denormalizingFieldMapping)) {
                continue;
            }

            $subPath = $this->getSubPathByName($path, $name);

            $fieldDenormalizer->denormalizeField($subPath, $object, $value, $this, $context);
        }

        if ([] !== $data && !$context->isAllowedAdditionalFields()) {
            throw DenormalizerException::createNotAllowedAddtionalFields(
                $this->getSubPathsByNames($path, array_keys($data))
            );
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

        throw DenormalizerException::createMissingMapping($class);
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
