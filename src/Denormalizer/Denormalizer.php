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
    private const TYPE = '_type';

    private LoggerInterface $logger;

    public function __construct(
        private DenormalizerObjectMappingRegistryInterface $denormalizerObjectMappingRegistry,
        ?LoggerInterface $logger = null
    ) {
        $this->logger = $logger ?? new NullLogger();
    }

    /**
     * @param array<string, null|array|bool|float|int|string> $data
     *
     * @throws DeserializerLogicException
     * @throws DeserializerRuntimeException
     */
    public function denormalize(
        object|string $object,
        array $data,
        ?DenormalizerContextInterface $context = null,
        string $path = ''
    ): object {
        $context ??= DenormalizerContextBuilder::create()->getContext();

        /** @var null|string $type */
        $type = $this->getType($data, $path);

        unset($data[self::TYPE]);

        $class = \is_object($object) ? $object::class : $object;
        $objectMapping = $this->getObjectMapping($class);

        if (!\is_object($object)) {
            $object = $this->createNewObject($objectMapping, $path, $type);
        }

        $additionalFields = array_flip(array_keys($data));
        foreach ($objectMapping->getDenormalizationFieldMappings($path, $type) as $denormalizationFieldMapping) {
            $name = $denormalizationFieldMapping->getName();

            unset($additionalFields[$name]);

            $this->denormalizeField($context, $denormalizationFieldMapping, $path, $name, $data, $object);
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
     * @param array<string, null|array|bool|float|int|string> $data
     */
    private function getType(array $data, string $path): ?string
    {
        if (!isset($data[self::TYPE])) {
            return null;
        }

        $type = $data[self::TYPE];

        if (\is_string($type)) {
            return $type;
        }

        $exception = DeserializerRuntimeException::createTypeIsNotAString($path, \gettype($type));

        $this->logError($exception);

        throw $exception;
    }

    /**
     * @throws DeserializerLogicException
     */
    private function getObjectMapping(string $class): DenormalizationObjectMappingInterface
    {
        try {
            return $this->denormalizerObjectMappingRegistry->getObjectMapping($class);
        } catch (DeserializerLogicException $exception) {
            $this->logError($exception);

            throw $exception;
        }
    }

    private function createNewObject(
        DenormalizationObjectMappingInterface $objectMapping,
        string $path,
        ?string $type = null
    ): object {
        $factory = $objectMapping->getDenormalizationFactory($path, $type);
        $object = $factory();

        if (\is_object($object)) {
            return $object;
        }

        $exception = DeserializerLogicException::createFactoryDoesNotReturnObject($path, \gettype($object));

        $this->logError($exception);

        throw $exception;
    }

    /**
     * @param array<string, null|array|bool|float|int|string> $data
     */
    private function denormalizeField(
        DenormalizerContextInterface $context,
        DenormalizationFieldMappingInterface $denormalizationFieldMapping,
        string $path,
        string $name,
        array $data,
        object $object
    ): void {
        if (!\array_key_exists($name, $data)) {
            if (!$context->isClearMissing()) {
                return;
            }

            $data[$name] = null;
        }

        $subPath = $this->getSubPathByName($path, $name);

        if (!$this->isCompliant($subPath, $object, $context, $denormalizationFieldMapping)) {
            return;
        }

        $this->logger->info('deserialize: path {path}', ['path' => $subPath]);

        $fieldDenormalizer = $denormalizationFieldMapping->getFieldDenormalizer();
        $fieldDenormalizer->denormalizeField($subPath, $object, $data[$name], $context, $this);
    }

    /**
     * @param array<int, int|string> $names
     */
    private function handleNotAllowedAdditionalFields(string $path, array $names): void
    {
        $exception = DeserializerRuntimeException::createNotAllowedAdditionalFields(
            $this->getSubPathsByNames($path, $names)
        );

        $this->logNotice($exception);

        throw $exception;
    }

    private function isCompliant(
        string $path,
        object $object,
        DenormalizerContextInterface $context,
        DenormalizationFieldMappingInterface $mapping
    ): bool {
        return $mapping->getPolicy()->isCompliant($path, $object, $context);
    }

    private function getSubPathByName(string $path, string $name): string
    {
        return '' === $path ? $name : $path.'.'.$name;
    }

    /**
     * @param array<int, int|string> $names
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

    private function logError(\Throwable $exception): void
    {
        $this->logger->error('deserialize: {exception}', ['exception' => $exception->getMessage()]);
    }

    private function logNotice(\Throwable $exception): void
    {
        $this->logger->notice('deserialize: {exception}', ['exception' => $exception->getMessage()]);
    }
}
