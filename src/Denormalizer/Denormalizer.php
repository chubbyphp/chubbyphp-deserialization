<?php

declare(strict_types=1);

namespace Chubbyphp\Deserialization\Denormalizer;

use Chubbyphp\Deserialization\DeserializerLogicException;
use Chubbyphp\Deserialization\DeserializerRuntimeException;
use Chubbyphp\Deserialization\Mapping\DenormalizationFieldMappingInterface;
use Chubbyphp\Deserialization\Mapping\DenormalizationObjectMappingInterface;
use Chubbyphp\Deserialization\Policy\GroupPolicy;
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

    public function __construct(
        DenormalizerObjectMappingRegistryInterface $denormalizerObjectMappingRegistry,
        ?LoggerInterface $logger = null
    ) {
        $this->denormalizerObjectMappingRegistry = $denormalizerObjectMappingRegistry;
        $this->logger = $logger ?? new NullLogger();
    }

    /**
     * @param object|string $object
     * @param array<mixed>  $data
     *
     * @throws DeserializerLogicException
     * @throws DeserializerRuntimeException
     *
     * @return object
     */
    public function denormalize($object, array $data, ?DenormalizerContextInterface $context = null, string $path = '')
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

        $additionalFields = array_flip(array_keys($data));
        foreach ($objectMapping->getDenormalizationFieldMappings($path, $type) as $denormalizationFieldMapping) {
            $name = $denormalizationFieldMapping->getName();

            $this->denormalizeField($context, $denormalizationFieldMapping, $path, $name, $data, $object);

            unset($additionalFields[$name]);
        }

        $allowedAdditionalFields = $context->getAllowedAdditionalFields();

        if (null !== $allowedAdditionalFields
            && [] !== $fields = array_diff(array_keys($additionalFields), $allowedAdditionalFields)
        ) {
            $this->handleNotAllowedAdditionalFields($path, $fields);
        }

        return $object;
    }

    /**
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
     * @return object
     */
    private function createNewObject(
        DenormalizationObjectMappingInterface $objectMapping,
        string $path,
        ?string $type = null
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
     * @param array<mixed> $data
     * @param object       $object
     */
    private function denormalizeField(
        DenormalizerContextInterface $context,
        DenormalizationFieldMappingInterface $denormalizationFieldMapping,
        string $path,
        string $name,
        array $data,
        $object
    ): void {
        if (!array_key_exists($name, $data)) {
            if (!$this->isResetMissingFields($context)) {
                return;
            }

            $data[$name] = null;
        }

        if (!$this->isCompliant($context, $denormalizationFieldMapping, $object)) {
            return;
        }

        if (!$this->isWithinGroup($context, $denormalizationFieldMapping)) {
            return;
        }

        $subPath = $this->getSubPathByName($path, $name);

        $this->logger->info('deserialize: path {path}', ['path' => $subPath]);

        $fieldDenormalizer = $denormalizationFieldMapping->getFieldDenormalizer();
        $fieldDenormalizer->denormalizeField($subPath, $object, $data[$name], $context, $this);
    }

    /**
     * @param array<int, string|int> $names
     */
    private function handleNotAllowedAdditionalFields(string $path, array $names): void
    {
        $exception = DeserializerRuntimeException::createNotAllowedAdditionalFields(
            $this->getSubPathsByNames($path, $names)
        );

        $this->logger->notice('deserialize: {exception}', ['exception' => $exception->getMessage()]);

        throw $exception;
    }

    /**
     * @param object $object
     */
    private function isCompliant(
        DenormalizerContextInterface $context,
        DenormalizationFieldMappingInterface $mapping,
        $object
    ): bool {
        if (!is_callable([$mapping, 'getPolicy'])) {
            return true;
        }

        return $mapping->getPolicy()->isCompliant($context, $object);
    }

    private function isWithinGroup(
        DenormalizerContextInterface $context,
        DenormalizationFieldMappingInterface $fieldMapping
    ): bool {
        if ([] === $groups = $context->getGroups()) {
            return true;
        }

        @trigger_error(
            sprintf(
                'Use "%s" instead of "%s::setGroups"',
                GroupPolicy::class,
                DenormalizerContextInterface::class
            ),
            E_USER_DEPRECATED
        );

        foreach ($fieldMapping->getGroups() as $group) {
            if (in_array($group, $groups, true)) {
                return true;
            }
        }

        return false;
    }

    private function getSubPathByName(string $path, string $name): string
    {
        return '' === $path ? $name : $path.'.'.$name;
    }

    private function isResetMissingFields(DenormalizerContextInterface $context): bool
    {
        return method_exists($context, 'isResetMissingFields') && $context->isResetMissingFields();
    }

    /**
     * @param array<int, string|int> $names
     *
     * @return array<int, string>
     */
    private function getSubPathsByNames(string $path, array $names): array
    {
        $subPaths = [];
        foreach ($names as $name) {
            $subPaths[] = $this->getSubPathByName($path, (string) $name);
        }

        return $subPaths;
    }
}
