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
     * @var DenormalizationObjectMappingInterface[]
     */
    private $objectMappings = [];

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @param DenormalizationObjectMappingInterface[]|DenormalizerObjectMappingRegistryInterface $objectMappings
     * @param LoggerInterface|null                                                               $logger
     */
    public function __construct(
        $objectMappings,
        LoggerInterface $logger = null
    ) {
        $this->logger = $logger ?? new NullLogger();

        if (is_array($objectMappings)) {
            foreach ($objectMappings as $objectMapping) {
                $this->addObjectMapping($objectMapping);
            }

            return;
        }

        if ($objectMappings instanceof DenormalizerObjectMappingRegistryInterface) {
            @trigger_error(
                sprintf(
                    'Use "%s" instead of "%s" as __construct argument',
                    DenormalizationObjectMappingInterface::class.'[]',
                    DenormalizerObjectMappingRegistryInterface::class
                ),
                E_USER_DEPRECATED
            );

            $this->denormalizerObjectMappingRegistry = $objectMappings;

            return;
        }

        throw new \TypeError(
            sprintf(
                '%s::__construct() expects parameter 1 to be %s|%s, %s given',
                self::class,
                DenormalizationObjectMappingInterface::class.'[]',
                DenormalizerObjectMappingRegistryInterface::class,
                is_object($objectMappings) ? get_class($objectMappings) : gettype($objectMappings)
            )
        );
    }

    /**
     * @param DenormalizationObjectMappingInterface $objectMapping
     */
    private function addObjectMapping(DenormalizationObjectMappingInterface $objectMapping)
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

        $type = null;
        if (isset($data['_type'])) {
            $type = $data['_type'];

            unset($data['_type']);
        }

        if (!is_object($object)) {
            $object = $this->createNewObject($objectMapping, $path, $type);
        }

        foreach ($objectMapping->getDenormalizationFieldMappings($path, $type) as $denormalizationFieldMapping) {
            $this->denormalizeField($context, $denormalizationFieldMapping, $path, $data, $object);

            unset($data[$denormalizationFieldMapping->getName()]);
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
        if (null !== $this->denormalizerObjectMappingRegistry) {
            try {
                return $this->denormalizerObjectMappingRegistry->getObjectMapping($class);
            } catch (DeserializerLogicException $exception) {
                $this->logger->error('deserialize: {exception}', ['exception' => $exception->getMessage()]);

                throw $exception;
            }
        }

        $reflectionClass = new \ReflectionClass($class);

        if (in_array('Doctrine\Common\Persistence\Proxy', $reflectionClass->getInterfaceNames(), true)) {
            $class = $reflectionClass->getParentClass()->name;
        }

        if (isset($this->objectMappings[$class])) {
            return $this->objectMappings[$class];
        }

        $exception = DeserializerLogicException::createMissingMapping($class);

        $this->logger->error('deserialize: {exception}', ['exception' => $exception->getMessage()]);

        throw $exception;
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
