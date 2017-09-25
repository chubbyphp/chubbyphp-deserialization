<?php

declare(strict_types=1);

namespace Chubbyphp\Deserialization\Denormalizer;

use Chubbyphp\Deserialization\Accessor\AccessorInterface;

final class FieldDenormalizer implements FieldDenormalizerInterface
{
    /**
     * @var AccessorInterface
     */
    private $accessor;

    /**
     * @var array
     */
    private $groups;

    /**
     * @param AccessorInterface $accessor
     * @param array             $groups
     */
    public function __construct(AccessorInterface $accessor = null, array $groups = [])
    {
        $this->accessor = $accessor;
        $this->groups = $groups;
    }

    /**
     * @param string                            $path
     * @param object                            $object
     * @param mixed                             $value
     * @param DenormalizerInterface|null        $denormalizer
     * @param DenormalizerContextInterface|null $context
     */
    public function denormalizeField(
        string $path,
        $object,
        $value,
        DenormalizerInterface $denormalizer = null,
        DenormalizerContextInterface $context = null
    ) {
        $this->accessor->setValue($object, $value);
    }

    /**
     * @return array
     */
    public function getGroups(): array
    {
        return $this->groups;
    }
}
