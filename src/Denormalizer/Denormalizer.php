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

        $fieldDenormalizerMappings = $this->getFieldDenormalizerMappings($objectMapping);

        foreach ($data as $field => $value) {
            $subPath = '' === $path ? $field : $path.'.'.$field;

            if (!isset($fieldDenormalizerMappings[$field])) {
                if (!$context->isAllowedAdditionalFields()) {
                    throw DenormalizerException::createNotAllowedAddtionalField($subPath);
                }

                continue;
            }

            $fieldDenormalizer = $fieldDenormalizerMappings[$field]->getFieldDenormalizer();

            if ($this->isWithinGroup($context, $fieldDenormalizer)) {
                $fieldDenormalizer->denormalizeField($subPath, $object, $value, $this, $context);
            }
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
     * @param DenormalizingObjectMappingInterface $objectMapping
     *
     * @return DenormalizingFieldMappingInterface[]
     */
    private function getFieldDenormalizerMappings(DenormalizingObjectMappingInterface $objectMapping): array
    {
        $fieldMappings = [];
        foreach ($objectMapping->getDenormalizingFieldMappings() as $denormalizingFieldMapping) {
            $fieldMappings[$denormalizingFieldMapping->getName()] = $denormalizingFieldMapping;
        }

        return $fieldMappings;
    }

    /**
     * @param DenormalizerContextInterface $context
     * @param FieldDenormalizerInterface   $fieldDenormalizer
     *
     * @return bool
     */
    private function isWithinGroup(
        DenormalizerContextInterface $context,
        FieldDenormalizerInterface $fieldDenormalizer
    ): bool {
        if ([] === $groups = $context->getGroups()) {
            return true;
        }

        foreach ($fieldDenormalizer->getGroups() as $group) {
            if (in_array($group, $groups, true)) {
                return true;
            }
        }

        return false;
    }
}
